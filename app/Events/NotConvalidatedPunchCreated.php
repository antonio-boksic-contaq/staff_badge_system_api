<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;



class NotConvalidatedPunchCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct()
    {
        Log::info(config('broadcasting.connections.pusher'));


        // Nessun parametro necessario, poiché inviamo solo un messaggio statico
    }

    public function broadcastOn()
    {
        return new Channel('punch-channel');
    }

    public function broadcastAs()
    {
        return 'punch.created';
    }

    public function broadcastWith()
    {
        return [
            'message' => "C'è un nuovo punch non convalidato",
        ];
    }
}