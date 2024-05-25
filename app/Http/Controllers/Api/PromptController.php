<?php

namespace App\Http\Controllers\Api;

use App\Events\PromptCreated;
use App\Models\Chat;
use App\Models\Prompt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
    public function store(Request $request, $chat_id)
    {
        $user_id = Auth::user()->id;
        $chat = Chat::find($chat_id);

        if ($chat->user_id != $user_id) {
            return response('Unathorized',400);
        }
        $Validator = Validator::make($request->all(), [
            'prompt_content' => 'required|string',
            'archived' => 'boolean',
        ]);

        if($Validator->fails()){
            return response('Something Went Wrong!',400);
        }


        $prompt=Prompt::create([
            'chat_id'=>$chat_id,
            'prompt_content'=>$request['prompt_content'],
            'archived'=>$request['archived'],
        ]);

        broadcast(new PromptCreated($prompt));

        return response()->json([
            'status' => 1,
            'message' => 'prompt Created Successfully',
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
    public function update(Request $request, Prompt $prompt)
    {
        $request->validate([
            'prompt_content' => 'sometimes|required|string|max:10000'
        ]);
    
        // Archive the current prompt
        $prompt->update(['archived' => true]);
    
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
