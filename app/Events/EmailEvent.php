<?php

// app/Events/EmailEvent.php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $pdfpath;

    /**
     * Create a new event instance.
     *
     * @param User $user
     */
    public function __construct(User $user, $pdfpath )
    {
        $this->user = $user;
        $this->pdfpath = $pdfpath;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
