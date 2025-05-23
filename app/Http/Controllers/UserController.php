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
    $users = User::all(); 

    return response()->json(UserResource::collection($users));
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


    $user->assignRole('staff');

    return response()->json(new UserResource($user));
}

public function checkIn(Request $request)
    {
        $user = $request->user();

        // Evita doppi check-in nello stesso giorno
        $today = Carbon::today();
        $existing = DB::table('time_logs')
            ->where('user_id', $user->id)
            ->whereDate('check_in', $today)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Hai già fatto il check-in oggi.'], 409);
        }

        // Inserisci nuovo check-in
        DB::table('time_logs')->insert([
            'user_id'   => $user->id,
            'check_in'  => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Check-in registrato.']);
    }

}
