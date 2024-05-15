<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class ChatController
{
    /**
     * Display a listing of the resource.
     */
    public function index() // get 
    {
        $chats = Chat::with(['user', 'prompts', 'responses'])->get(); // eager loading method 
        return response()->json($chats);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) // post
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'chat_title' => 'required|string',
            'isPinned' => 'boolean'
        ]);

        $chat = Chat::create($request->all());
        return response()->json([
            'status' => 1,
            'message' => 'Chat Created Successfully',
            'data' => $chat
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Chat $chat)
    {
        return response()->json($chat);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chat $chat)
    {
        $request->validate([
            'chat_title' => 'sometimes|required|string',
            'isPinned' => 'sometimes|required|boolean', 
            'promprt_content' => 'sometimes|required|string',
            'response_content' => 'sometimes|required|string'
        ]);

        $chat->update($request->only(['chat_title', 'isPinned']));

        if ($request->has('prompt_content')) {
            $prompt = new Prompt([
                'content' => $request->input('prompt_content'),
            ]);
            $chat->prompts()->save($prompt);
        }

        if($request->has('response_content')){
            $response = new Response([
                'content' => $request->input('response_content'),
            ]);
            $chat->responses()->save($response);
        }

        return response()->json($chat->load(['prompts', 'responses']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chat $chat)
    {
        $chat->delete();
        return response()->json(['message' => 'Chat Deleted Successfully']);
    }

    public function search(Request $request)
    {
        $query = Chat::query();

        if ($search = $request->query('q')) {
            $query->where('chat_title', 'like', "%{$search}%")
                  ->orWhereHas('prompts', function ($q) use ($search) {
                      $q->where('prompt_content', 'like', "%{$search}%");
                  })
                  ->orWhereHas('responses', function ($q) use ($search) {
                      $q->where('response_content', 'like', "%{$search}%");
                  });
        }

        $chats = $query->with(['user', 'prompts', 'responses'])->get();

        return response()->json($chats);
    }
}
