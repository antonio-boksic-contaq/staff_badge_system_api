<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BadgeController extends Controller
{
    public function index(Request $request){

        $date = $request->input('date') ?? Carbon::today()->toDateString();

        $userId = $request->user()->id;
        $role = $request->user()->getRoleNames()[0]; // es. ['admin'], ['dipendente'], ecc.

        $query = DB::table('time_logs')
        ->join('users', 'time_logs.user_id', '=', 'users.id')
        ->orderBy('time_logs.check_in')
        ->select(
            'time_logs.id',
            'time_logs.user_id',
            'users.name',
            'users.surname',
            'time_logs.check_in',
            'time_logs.check_out',
            'time_logs.notes'
        );

        if ($role === 'Staff') {
            $query->where('time_logs.user_id', $userId);
        }

        if ($request->has("exactDate")) {
        $query->whereDate('time_logs.check_in', $request->exactDate);
        }

        $timeLogs = $query->get();

        if ($request->has('exactDate')) {
        $timeLogs->transform(function ($item) {
        $item->check_in = $item->check_in ? Carbon::parse($item->check_in)->format('H:i:s') : null;
        $item->check_out = $item->check_out ? Carbon::parse($item->check_out)->format('H:i:s') : null;
        return $item;
        });
}

        return response()->json($timeLogs);
    }


    public function GetTimeLogs(Request $request){
        $user_id = $request->user()->id;

        $time_logs = DB::select("SELECT * from time_logs 
        WHERE user_id = $user_id
        AND DATE(check_in) = DATE(NOW()) 
        ")[0] ?? null;

       return response()->json($time_logs);

    }

    public function checkIn(Request $request){
        $user = $request->user();

        $today = Carbon::today();
        $existing = DB::table('time_logs')
            ->where('user_id', $user->id)
            ->whereDate('check_in', $today)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Hai già fatto il check-in oggi.'], 409);
        }

        DB::table('time_logs')->insert([
            'user_id'   => $user->id,
            'check_in'  => now(),
        ]);

        return response()->json(['message' => 'Check-in registrato.']);
    }

    public function checkOut(Request $request){
        $user = $request->user();

        
        $record = DB::table('time_logs')
            ->where('user_id', $user->id)
            ->whereNull('check_out')
            ->orderByDesc('check_in')
            ->first();

        if (!$record) {
            return response()->json(['message' => 'Nessun check-in trovato.'], 404);
        }

        DB::table('time_logs')
            ->where('id', $record->id)
            ->update([
                'check_out'  => now(),

            ]);

        return response()->json(['message' => 'Check-out registrato.']);
    }

    public function createNote(Request $request){


        $user = $request->user();
        $today = Carbon::today(); 

        $record = DB::table('time_logs')
        ->where('user_id', $user->id)
        ->whereDate('check_in', $today)
        ->orderByDesc('check_in')
        ->first();

        if (!$record) {
            return response()->json(['message' => 'Nessun check-in trovato per oggi'], 404);
        }

        DB::table('time_logs')
        ->where('id', $record->id)
        ->update([
            'notes' => $request->notes,
           
        ]);

        return response()->json(['message' => 'Note aggiunte correttamente.']);

    }

    
}
