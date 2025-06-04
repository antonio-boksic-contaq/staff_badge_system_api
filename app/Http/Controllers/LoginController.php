<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\PasswordRecovery;

class LoginController extends Controller
{
    public function login(LoginRequest $request){
       
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
          throw ValidationException::withMessages([
            'error' => ['Le credenziali inserite sono sbagliate'],
          ]);
        }
    
        $token = $user->createToken($request->email)->plainTextToken;
    
        return response()->json(['token' => $token], 200);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        Auth::guard('web')->logout();
        return response()->json(['success' => true], 200);
    }

    public function passwordRecovery(Request $request){
        $request->validate(
          ['email' => 'required|exists:users,email,deleted_at,NULL'],
          [
            'email.required' => 'L\'indirizzo email è obbligatorio',
            'email.exists' => 'Non abbiamo trovato nessun utente con questa email',
          ]
        );
        $newPassword = Str::upper(Str::random(8));
        $user = User::where('email',$request->get('email'))->first();
        $user->update(['password' => Hash::make($newPassword)]);
        // Mail::to($user->email)->send(new PasswordRecovery($user, $newPassword));
        try {
    Mail::to($user->email)->send(new PasswordRecovery($user, $newPassword));
} catch (\Exception $e) {
    logger()->error('Errore invio mail: ' . $e->getMessage());
}
    
        return response()->json(['success' => true], 200);
    }
}
