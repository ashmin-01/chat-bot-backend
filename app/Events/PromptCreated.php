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

    private Prompt $prompt;

    /**
     * Create a new event instance.
     */
    public function __construct(Prompt $prompt)
    {
        $this->prompt = $prompt;
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
        // Encode the data to JSON with unescaped Unicode and then decode it back to an array
        $data = [
            'chat_id' => $this->prompt->chat_id,
            'prompt' => $this->prompt->toArray(),
        ];

        return json_decode(json_encode($data, JSON_UNESCAPED_UNICODE), true);
    }
}
