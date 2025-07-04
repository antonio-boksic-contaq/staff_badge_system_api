<?php

namespace App\Http\Controllers;

use App\Events\NotConvalidatedPunchCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Punch;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PunchesExport;
use App\Mail\LateCheckOutNotificationMail;
use Illuminate\Support\Facades\Mail;


class BadgeController extends Controller
{

    //questa sarebbe /getAllPunches
    public function index(Request $request){

        // dd($request);

            $date = $request->input('date') ?? Carbon::today()->toDateString();

            $userId = $request->user()->id; //questo è id utente che manda richiesta
            $user_id_for_detail = $request->user_id; //questo è id utente che viene cercato da admin
            $role = $request->user()->getRoleNames()[0]; // es. ['admin'], ['dipendente'], ecc.

            $query = DB::table('punches')
            ->join('users', 'punches.user_id', '=', 'users.id')
            ->orderBy('punches.check_in')
            ->select(
                'punches.id',
                'punches.user_id',
                'users.name',
                'users.surname',
                'punches.check_in',
                'punches.check_out',
                'punches.notes',
                'punches.co_accepted'
            );

            // se richiesta viene effettuata da Staff gli passo solo i dati del relativo utente
            if ($role === 'Staff') {
                $query->where('punches.user_id', $userId);
            }

            // questa viene chiamata da admin quando vuole vedere calendario di qualche membro dello staff
            if($user_id_for_detail){
                $query->where('punches.user_id', $user_id_for_detail);
            }

            //  questo exactdate lo manda admin quando vuole vedere tabella con timbrature della giornata indicata
            if ($request->has("exactDate")) {
                $query->whereDate('punches.check_in', $request->exactDate);
            }

            if($request->has("not-convalidated")){
                $query->where('punches.co_accepted', 0);
            }

            if ($request->has('startDate')) {
                $query->whereDate('check_in', '>=', $request->startDate);
            }

            if ($request->has('endDate')) {
                $query->whereDate('check_in', '<=', $request->endDate);
            }

            $punches = $query->get();

            if($request->has('export')) {
                // dd("è stato richiesto export");
                 return Excel::download(new PunchesExport($punches), 'timbrature.xlsx');
            }

            if ($request->has('exactDate')) {
                $punches->transform(function ($item) {
                $item->check_in = $item->check_in ? Carbon::parse($item->check_in)->format('H:i:s') : null;
                $item->check_out = $item->check_out ? Carbon::parse($item->check_out)->format('H:i:s') : null;
                return $item;
                });
            }

        return response()->json($punches);
    }


    // la uso questa?
    public function GetTimeLogs(Request $request){
        $user_id = $request->user()->id;

        $punches = DB::select("SELECT * from punches 
        WHERE user_id = $user_id
        AND DATE(check_in) = DATE(NOW()) 
        ")[0] ?? null;

       return response()->json($punches);

    }

    // public function checkIn(Request $request){
    //     $user = $request->user();

    //     $today = Carbon::today();
    //     $existing = DB::table('punches')
    //         ->where('user_id', $user->id)
    //         ->whereDate('check_in', $today)
    //         ->first();

    //     if ($existing) {
    //         return response()->json(['message' => 'Hai già fatto il check-in oggi.'], 409);
    //     }

    //     DB::table('punches')->insert([
    //         'user_id'   => $user->id,
    //         'check_in'  => now(),
    //     ]);

    //     // return response()->json(['message' => 'Check-in registrato.']);
    // }

    public function checkIn(Request $request){
        
    $user = $request->user();
    $today = now()->startOfDay();

    $existing = Punch::where('user_id', $user->id)
        ->whereDate('check_in', $today)
        ->first();

    if ($existing) {
        return response()->json(['message' => 'Hai già fatto il check-in oggi.'], 409);
    }

    Punch::create([
        'user_id'   => $user->id,
        'check_in'  => now(),
    ]);

    return response()->json(['message' => 'Check-in registrato.']);
}


    public function checkOut(Request $request){
        $user = $request->user();

        
        $record = DB::table('punches')
            ->where('user_id', $user->id)
            ->whereNull('check_out')
            ->orderByDesc('check_in')
            ->first();

        if (!$record) {
            return response()->json(['message' => 'Nessun check-in trovato.'], 404);
        }

        DB::table('punches')
            ->where('id', $record->id)
            ->update([
                'check_out'  => now(),

            ]);

        return response()->json(['message' => 'Check-out registrato.']);
    }

    public function createNote(Request $request){


        $user = $request->user();
        $today = Carbon::today(); 

        $record = DB::table('punches')
        ->where('user_id', $user->id)
        ->whereDate('check_in', $today)
        ->orderByDesc('check_in')
        ->first();

        if (!$record) {
            return response()->json(['message' => 'Nessun check-in trovato per oggi'], 404);
        }

        DB::table('punches')
        ->where('id', $record->id)
        ->update([
            'notes' => $request->notes,
           
        ]);

        return response()->json(['message' => 'Note aggiunte correttamente.']);

    }

    public function lateCheckOut(Request $request){
         // dd($request->user()->id);
        $punch =   Punch::where('user_id', $request->user()->id)
        ->whereNotNull('check_in')
        ->whereNull('check_out')
        ->orderByDesc('check_in')
        ->first();

        // Prende solo la data (Y-m-d) dal check_in esistente
        $checkInDate = Carbon::parse($punch->check_in)->toDateString(); // es. "2025-06-01"

        // Combina la data del check-in con l'orario ricevuto
        $checkOutDateTime = Carbon::createFromFormat('Y-m-d H:i', $checkInDate . ' ' . $request->time);

            // Aggiorna i campi
        $punch->check_out = $checkOutDateTime;
        $punch->co_accepted = 0;
        $punch->save();

    
        // questo mi servirebbe per gestire eventi tramite websockets ma non riesco a configurare macchina...
        // event(new NotConvalidatedPunchCreated());
        //TODO disattivato websockets, Lucia dice che vuole ricevere mail piuttosto che notifica in app
        Mail::to('lucia.cofini@contaq.it')->send(new LateCheckOutNotificationMail($punch));


        // return response()->json(['message' => 'Late check-out registrato con successo.']);


        return $punch;
    }

    public function convalidatePunch(Request $request) {
        // dd($request);
              $punch =   Punch::where('id', $request->id)
        ->first();

            $punch->ci_accepted = 1;
        $punch->co_accepted = 1;
        $punch->save();

        return response()->json(['message' => 'Punch convalidato con successo.']);

        // dd($punch);
    }

    
}
