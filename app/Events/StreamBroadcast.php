<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StreamBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $response_content;

    public function __construct(array $data)
    {
        $this->response_content = $data['response_content'];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('privat-chat.1');
    }

    public function broadcastAs()
    {
        return 'stream.sent';
    }

    public function broadcastWith()
    {
        return [
            'response_content' => $this->response_content,
        ];
    }
}
