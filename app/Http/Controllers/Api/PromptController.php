<?php

namespace App\Http\Controllers\Api;

use App\Models\Chat;
use App\Models\Prompt;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Events\PromptCreated;
use Illuminate\Support\Facades\Auth;

class PromptController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $prompts = Prompt::with(['chat', 'responses'])->get(); // eager loading method
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

        $response = $this->sendPromptToLangChain($prompt->prompt_content);

        $this->SendPrompt($prompt);

        return response()->json([
            'status' => 1,
            'message' => 'Prompt Created Successfully',
            'data' => $prompt,
            'response' => $response
        ], 201);
    }

    private function SendPrompt(Prompt $prompt){
        $chat_id = $prompt->chat_id;
        broadcast(new PromptCreated($prompt));
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
