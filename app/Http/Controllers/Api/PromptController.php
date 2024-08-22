<?php

namespace App\Http\Controllers\Api;

use App\Models\Chat;
use App\Models\Prompt;
use GuzzleHttp\Client;
use App\Models\Response;
use Illuminate\Http\Request;
use App\Events\PromptCreated;
use App\Events\ResponseCreated;
use App\Events\StreamBroadcast;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PromptController
{
    /**
     * Display a listing of the resource.
     */
    public function index($chat_id)
    {
        $prompts = Prompt::where('chat_id' , $chat_id)
        ->with('responses')
        ->get(); // eager loading method
        return response()->json($prompts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'chat_id' => 'nullable|integer',
            'prompt_content' => 'required|string|max:10000'

        ]);

        $chat = Chat::find($request->chat_id);
        $user_id = Auth::user()->id;

        if (!$chat) {
            $chat = Chat::create([
                'user_id'=>$user_id,
                'chat_title'=>'new chat',
                'isPinned'=>0,
            ]);
        }

        $prompt = Prompt::create($request->all());

        $this->SendPrompt($prompt);

        $chatbotUrl = env('CHATBOT_API_URL');

        $userMessage = $request->input('prompt_content');
        $conversationId = $request->chat_id;

        if ($chatbotUrl) {
            $response = Http::timeout(100)->withOptions(['verify' => false])->asForm()->post($chatbotUrl . '/chat/get-response', [
                'question' => $userMessage,
                'conversation_id' => (string) $conversationId
            ]);


            $responseData = $response->json();

        // Save and broadcast the response
        $fullResponse = $responseData['response'];
        $newResponse = Response::create([
            'chat_id' => $request->chat_id,
            'prompt_id' => $prompt->id,
            'response_content' => $fullResponse,
            'response_status' => null, // Default value, can be updated later
        ]);

        $this->SendResponse($newResponse);

        $titleResponse = Http::withOptions(['verify' => false])->post($chatbotUrl . '/chat/generate-title', [
            'message' => $userMessage,
        ]);

        $titleData = $titleResponse->json();
        $generatedTitle = $titleData['title'] ?? 'Chat'; // Fallback to 'Chat' if no title is generated

        // Update the chat title
        $chat->update([
            'chat_title' => $generatedTitle,
        ]);

        }

        return response()->json(['status' => 'Message broadcasted']);

    }

    public function streamResponse()
    {
        $chatbotUrl = env('CHATBOT_API_URL');

        if ($chatbotUrl) {
            $response = new StreamedResponse(function () use ($chatbotUrl) {
                // Output buffering settings
                ob_implicit_flush(true);
                ob_end_flush();

                $stream = Http::withOptions(['verify' => false])
                              ->asForm()
                              ->get($chatbotUrl . '/chat/stream-response');

                Log::info('Stream response status: ' . $stream->status());
                Log::info('Stream response body: ' . $stream->body());

                if ($stream->successful()) {
                    $body = $stream->getBody();

                    // Stream the data
                    while (!$body->eof()) {
                        $chunk = $body->read(1024); // Reading the stream in chunks of 1024 bytes
                        echo $chunk;
                        flush();
                    }
                } else {
                    echo "Error streaming response.";
                    flush();
                }
            });

            $response->headers->set('Content-Type', 'text/plain');
            $response->headers->set('Cache-Control', 'no-cache');
            $response->headers->set('Connection', 'keep-alive');

            return $response;
        }

        return response()->json(['status' => 'Chatbot URL not configured']);
    }

    public function streamResponseTest()
    {
        $response = new StreamedResponse(function () {
            // Output buffering settings
            ob_implicit_flush(true);
            ob_end_flush();

            // Stream data
            echo "Streaming started...\n";
            flush();

            sleep(0); // Simulate delay

            echo "Streaming continues...\n";
            flush();

            sleep(0); // Simulate more delay

            echo "Streaming ended.";
            flush();
        });

        // Set headers for streaming
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }


    private function SendPrompt(Prompt $prompt){
        $chat_id = $prompt->chat_id;
        broadcast(new PromptCreated($prompt));
    }

    private function SendResponse(Response $response){
        $chat_id = $response->chat_id;
        broadcast(new ResponseCreated($response));
    }

    /**
     * Display the specified resource.
     */
    public function show(Prompt $prompt)
    {
        return response()->json($prompt);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $prompt_id)
    {
        $request->validate([
            'prompt_content' => 'sometimes|required|string|max:10000'
        ]);

        // Archive the current prompt
        $prompt = Prompt::find($prompt_id);
        $prompt->update(['archived' => true]);
        $prompt->save();

        // Create a new prompt
        $newPrompt = Prompt::create([
            'chat_id' => $prompt->chat_id,
            'prompt_content' => $request->prompt_content,
            'archived' => false
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'New Prompt Created and Old Prompt Archived',
            'data' => $newPrompt
        ], 201);
    }

    private function sendPromptToLangChain($promptContent)
    {
        $client = new Client();
        $apiUrl = 'http://localhost:8000/query'; // Ensure this matches your FastAPI endpoint

        try {
            $response = $client->post($apiUrl, [
                'json' => [
                    'question' => $promptContent,
                ]
            ]);

            $responseBody = json_decode($response->getBody(), true);
            return $responseBody['response'] ?? 'No response from LangChain API';
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

}



        //$generatedResponseContent = $this->sendPromptToLangChain($prompt->prompt_content);

        // $response = $chat->responses()->create([
        //     'prompt_id' => $prompt->id,
        //     'response_content' => $generatedResponseContent,
        //     'response_status' => null, // Default value, can be updated later
        // ]);


/*
        $response = new StreamedResponse(function () use ($prompt, $chat) {
            // Start the streaming response
            $generatedResponseContent = $this->sendPromptToLangChain($prompt->prompt_content);
            $response = $chat->responses()->create([
                'prompt_id' => $prompt->id,
                'response_content' => $generatedResponseContent,
                'response_status' => null, // Default value, can be updated later
            ]);


            $this->SendPrompt($prompt);
            echo json_encode([
                'status' => 1,
                'message' => 'Prompt Created Successfully',
                'data' => $prompt,
                'response' => $response
            ]);
            // Flush the output buffer to send the current chunk to the client
            flush();
        });

        $response->headers->set('Content-Type', 'application/json');

        return $response;
*/
        // $this->SendResponse($response);
