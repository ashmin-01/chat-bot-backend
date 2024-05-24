<?php

namespace App\Http\Controllers\Api;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController
{
    /**
     * Display a listing of the resource.
     */


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request ,$response_id)
    {
        $request->validate([
            'chat_id' => 'required|exists:chats,id',
            'context' => 'required|string|max:10000'

        ]);

        $feedback = Feedback::create($request->all());
        return response()->json([
            'status' => 1,
            'message' => 'feedback sent Successfully',
            'data' => $feedback
        ], 201);
    }

}
