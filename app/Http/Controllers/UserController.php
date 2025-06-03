<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use Carbon\Carbon;


class UserController extends Controller
{

public function index(Request $request)
{
    $role = $request->role;

    if ($role) {
        $users = User::role($role)->get();
    } else {
        $users = User::role('Staff')->get();
        // $users = User::all();
    }

    return response()->json(UserResource::collection($users));
}

public function show(Request $request, User $user) {
    // dd($user);
    return response()->json(new UserResource($user));
}


    public function update(UserRequest $request, User $user)
    {
        $user->update($request->all());
        return response()->json(new UserResource($user));
    }

public function delete(User $user)
{
        return $user->delete() ? 
        response()->json(['error' => 'false']) :
        response()->json(['error' => 'true']);
}


public function store(UserRequest $request)
{

    $user = User::create([
        'name' => $request->name,
        'surname' => $request->surname,
        'email' => $request->email,
        //quando admin crea utente di default gli mettiamo la password: "password", poi se la cambia
        'password' => Hash::make("password"),
    ]);


    $user->assignRole('Staff');

    return response()->json(new UserResource($user));
}

public function checkIn(Request $request){
    $user = $request->user();

    // Evita doppi check-in nello stesso giorno
    $today = Carbon::today();
    $existing = DB::table('punches')
        ->where('user_id', $user->id)
        ->whereDate('check_in', $today)
        ->first();

    if ($existing) {
        return response()->json(['message' => 'Hai già fatto il check-in oggi.'], 409);
    }

    // Inserisci nuovo check-in
    DB::table('punches')->insert([
        'user_id'   => $user->id,
        'check_in'  => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json(['message' => 'Check-in registrato.']);
    }

    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|confirmed|min:6'
        ],[
            'password.required' => 'Il campo password è obbligatorio.',
            'password.confirmed' => 'Le password non corrispondono.',
            'password.min' => 'La password deve contenere almeno :min caratteri.',
        ]);
        $user->update(['password' => Hash::make($request->get('password'))]);
        return response()->json(new UserResource($user));
    }

}


