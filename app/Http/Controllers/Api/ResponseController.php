<?php

namespace App\Http\Controllers\Api;

use App\Models\Chat;
use App\Models\Prompt;
use App\Models\Feedback;
use App\Models\Response;
use Illuminate\Http\Request;
use App\Events\PromptCreated;
use App\Events\ResponseCreated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ResponseController
{
    /**
     * Display a listing of the resource.
     */
    /*
    public function BadResponses() // problem
    {
        // Fetch responses with 'Dislike' status and eager load related models
        $responses = Response::with(['prompt', 'feedback'])
                             ->where('response_status', 'Dislike')
                             ->get();

        // Return the view with the fetched data

        return view('home.tables', ['responses' => $responses]);
    }

    public function RegeneratedResponses()
    {
        // Fetch feedbacks that have a non-null regenerate_review
        $feedbacks = Feedback::with([
            'response.prompt',
            'response.prompt.responses' => function($query) {
                $query->where('archived', true); // Fetch only the old (archived) response
            }
        ])->whereNotNull('regenerate_review')->get();

        // Pass the feedback data to the view
        return view('home.tables', ['feedbacks' => $feedbacks]);
    }
    */


    public function viewTable()
    {
        // Fetch responses with 'Dislike' status
        $badResponses = Response::with(['prompt', 'feedback'])
                                ->where('response_status', 'Dislike')
                                ->get();

        // Fetch feedbacks that have a non-null regenerate_review
        $regeneratedFeedbacks = Feedback::with([
            'response.prompt',
            'response.prompt.responses' => function($query) {
                $query->where('archived', true); // Fetch only the old (archived) response
            }
        ])->whereNotNull('regenerate_review')->get();

        // Pass the data to the view
        return view('home.tables', [
            'badResponses' => $badResponses,
            'regeneratedFeedbacks' => $regeneratedFeedbacks
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|exists:chats,id',
            'prompt_id' => 'required|exists:prompts,id',
            'response_content' => 'required|string|max:10000',
            'response_status' => 'sometimes'

        ]);

        $response = Response::create($request->all());

        $this->SendResponse($response);

        //dd(broadcast(new ResponseCreated($response)));

        return response()->json([
            'status' => 1,
            'message' => 'Response Created Successfully',
            'data' => $response
        ], 201);

    }


    /**
     * Display the specified resource.
     */
    public function show(Response $response)
    {
        return response()->json($response);
    }
    /**
     * Update the specified resource in storage.
     */
    public function regenrate($response_id)
    {
        // Retrieve the existing response
        $existingResponse = Response::find($response_id);

        if (!$existingResponse) {
            return response()->json(['error' => 'Response not found'], 404);
        }

        // Archive the existing response
        $existingResponse->archived = true;
        $existingResponse->save();

        // Retrieve the related prompt
        $prompt = Prompt::find($existingResponse->prompt_id);

        if (!$prompt) {
            return response()->json(['error' => 'Prompt not found'], 404);
        }

        // Prepare for chatbot interaction
        $userMessage = $prompt->prompt_content;
        $conversationId = $existingResponse->chat_id;

        $chatbotUrl = env('CHATBOT_API_URL');

        if ($chatbotUrl) {
            // Send the prompt data to the chatbot API
            $response = Http::timeout(120)->withOptions(['verify' => false])->asForm()->post($chatbotUrl . '/chat/get-response', [
                'question' => $userMessage,
                'conversation_id' => (string) $conversationId,
                'is_en' => false,  // Set this based on your needs
            ]);

            $responseData = $response->json();

            // Save and broadcast the new response
            $fullResponse = $responseData['response'];
            $newResponse = Response::create([
                'chat_id' => $existingResponse->chat_id,
                'prompt_id' => $prompt->id,
                'response_content' => $fullResponse,
                'response_status' => null, // Default value, can be updated later
            ]);

            $this->SendResponse($newResponse);

            // Generate a new title for the chat if needed
            $titleResponse = Http::withOptions(['verify' => false])->post($chatbotUrl . '/chat/generate-title', [
                'message' => $userMessage,
            ]);

            $titleData = $titleResponse->json();
            $generatedTitle = $titleData['title'] ?? 'Chat'; // Fallback to 'Chat' if no title is generated

            // Update the chat title
            $chat = Chat::find($existingResponse->chat_id);
            $chat->update([
                'chat_title' => $generatedTitle,
            ]);
        }

        return response()->json(['status' => 'Message broadcasted']);
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
 * Like or dislike a response.
 */
public function toggleLikeDislike(Request $request, Response $response)
{
    $request->validate([
        'action' => 'required|in:Like,Dislike'
    ]);

    $action = $request->input('action');

    if ($action === 'Like') {
        if ($response->response_status === 'Like') {
            // If already liked, set status to null
            $response->update(['response_status' => null]);
            $message = 'Like removed successfully';
        } else {
            // Otherwise, like the response
            $response->update(['response_status' => 'Like']);
            $message = 'Response Liked Successfully';
        }
    } elseif ($action === 'Dislike') {
        if ($response->response_status === 'Dislike') {
            // If already disliked, show a message
            $message = 'You cannot dislike the response again.';
        } else {
            $response->update(['response_status' => 'Like']);
            $message = 'Response Liked Successfully';
        }
    }

    return response()->json(['message' => $message]);

    return response()->json([
        'status' => 'success',
        'message' => $message,
        'data' => $response
    ]);
}


}
