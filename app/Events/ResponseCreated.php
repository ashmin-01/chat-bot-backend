<?php

namespace App\Events;

use App\Models\Response;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class ResponseCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    /*
    public $response;
    public $userId;
    */

    /**
     * Create a new event instance.
     *
     * @param Response $response
     */

    public function __construct(private Response $response)
    {

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new PrivateChannel('Chat.' . $this->response->chat_id);
    }

    public function broadcastAs()
    {
        return 'response.sent';
    }

    public function broadcastWith()
    {
        return [
            'chat_id' => $this->response->chat_id ,
            'response' => $this->response->toArray(),
        ];
    }
}
