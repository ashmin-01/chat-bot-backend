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
        // Log the incoming request data
        Log::info('Store method called with data:', $request->all());

        $request->validate([
            'chat_id' => 'nullable|integer',
            'prompt_content' => 'required|string|max:10000',
            'is_en' => 'required|boolean'
        ]);

        try {
            $chat = Chat::find($request->chat_id);
            $user_id = Auth::user()->id;
            Log::info('User ID: ' . $user_id);

            if (!$chat) {
                Log::info('Chat not found, creating a new chat.');
                $chat = Chat::create([
                    'user_id' => $user_id,
                    'chat_title' => 'new chat',
                    'isPinned' => 0,
                ]);
                Log::info('New chat created with ID: ' . $chat->id);
            }

            $prompt = Prompt::create($request->all());
            Log::info('Prompt created with ID: ' . $prompt->id);

            $this->SendPrompt($prompt);

            $chatbotUrl = env('CHATBOT_API_URL');

            $userMessage = $request->input('prompt_content');
            $conversationId = $request->chat_id;

            $is_english = $request->is_en;

            if ($chatbotUrl) {
                Log::info('Sending request to Chatbot API URL: ' . $chatbotUrl);

                $response = Http::timeout(100)->withOptions(['verify' => false])->asForm()->post($chatbotUrl . '/chat/get-response', [
                    'question' => $userMessage,
                    'conversation_id' => (string) $conversationId,
                    'is_en' => $is_english,  // Set this based on your needs
                ]);
                

                Log::info('Chatbot API response received.');
                $responseData = $response->json();

                if (isset($responseData['response'])) {
                    $fullResponse = $responseData['response'];
                } else {
                    Log::error('Expected "response" key not found in API response.', $responseData);
                    return response()->json(['status' => 'Error occurred', 'message' => 'Invalid API response'], 500);
                }
                
                $newResponse = Response::create([
                    'chat_id' => $request->chat_id,
                    'prompt_id' => $prompt->id,
                    'response_content' => $fullResponse,
                    'response_status' => null, // Default value, can be updated later
                ]);
                Log::info('Response saved with ID: ' . $newResponse->id);

                $this->SendResponse($newResponse);

                $titleResponse = Http::withOptions(['verify' => false])->post($chatbotUrl . '/chat/generate-title', [
                    'message' => $userMessage,
                ]);

                Log::info('Title generation response received.');

                $titleData = $titleResponse->json();
                $generatedTitle = $titleData['title'] ?? 'Chat'; // Fallback to 'Chat' if no title is generated

                // Update the chat title
                $chat->update([
                    'chat_title' => $generatedTitle,
                ]);
                Log::info('Chat title updated to: ' . $generatedTitle);
            }

            return response()->json(['status' => 'Message broadcasted']);
        } catch (\Exception $e) {
            // Log any exceptions that occur during the process
            Log::error('Error in store method:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['status' => 'Error occurred', 'message' => $e->getMessage()], 500);
        }
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

}