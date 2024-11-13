<?php

namespace App\Events;

use App\Models\Ticket;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class TicketBooked implements ShouldBroadcast
{
    use SerializesModels;

    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function broadcastOn()
    {
        return new Channel('show.' . $this->ticket->show_id);
    }

    public function broadcastWith()
    {
        return [
            'ticket_id' => $this->ticket->id,
            'seat_id' => $this->ticket->seat_id,
            'user_id' => $this->ticket->user_id,
            'price' => $this->ticket->price,
        ];
    }
}
