<?php

namespace App\Events;

use App\Models\Prompt;
use App\Models\Response;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class PromptCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    public $response;

    public function __construct(Response $response)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $other_user = $this->response->chat->participants()
        ->where("user_id", '<>' , Auth::user()->id)
        ->first();

        $participants = $this->response->chat->participants;
        return [
            new PresenceChannel('Messenger.' . $this->response->chat_id),
        ];
    }
}
