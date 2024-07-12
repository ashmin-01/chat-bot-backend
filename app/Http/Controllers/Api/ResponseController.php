<?php

namespace App\Http\Controllers\Api;

use App\Events\ResponseCreated;
use App\Models\Response;
use Illuminate\Http\Request;

class ResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function index() // problem
    {
        $responses = Response::with(['chat', 'prompt', 'feedback'])->get(); // eager loading method
        return response()->json($responses);
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

    private function SendResponse(Response $response){
        $chat_id = $response->chat_id;
        broadcast(new ResponseCreated($response));
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
    public function update(Request $request, Response $response)
    {
        $request->validate([
            'response_content' => 'required|string|max:10000'
        ]);

        // Logic to regenerate the response content: this will involve integrating with the chat model to generate the resposne
        // temp
        $response->update([
            'response_content' => $request->input('response_content')
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Response Updated Successfully',
            'data' => $response
        ]);
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
        $response->update(['response_status' => 'Like']);
        $message = 'Response Liked Successfully';
    } elseif ($action === 'Dislike') {
        $response->update(['response_status' => 'Dislike']);
        $message = 'Response Disliked Successfully';
    }

    return response()->json([
        'status' => 'success',
        'message' => $message,
        'data' => $response
    ]);
}


}
