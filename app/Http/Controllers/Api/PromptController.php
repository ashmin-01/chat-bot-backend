<?php

namespace App\Http\Controllers\Api;

use App\Models\Prompt;
use Illuminate\Http\Request;

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
            'chat_id' => 'required|exists:chats,id',
            'prompt_content' => 'required|string|max:10000'

        ]);

        $prompt = Prompt::create($request->all());
        return response()->json([
            'status' => 1,
            'message' => 'Prompt Created Successfully',
            'data' => $prompt
        ], 201);
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
