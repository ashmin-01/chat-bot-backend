<?php

namespace App\Events;

use App\Models\Prompt;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PromptCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(private Prompt $prompt)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new PrivateChannel('Chat.' . $this->prompt->chat_id);
    }

    public function broadcastAs()
    {
        return 'prompt.sent';
    }

    public function broadcastWith()
    {
        return [
            'chat_id' => $this->prompt->chat_id ,
            'prompt' => $this->prompt->toArray(),
        ];
    }
}
