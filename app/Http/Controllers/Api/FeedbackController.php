<?php

namespace App\Http\Controllers\Api;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $feedback = Feedback::with(['response'])->get(); // eager loading method
        return response()->json($feedback);
    }

    /**
     * Store a newly created feedback in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'response_id' => 'required|exists:responses,id',
            'feedback_type' => 'required|in:dislike,regenerate',
            'context' => 'nullable|string|max:10000',
            'regenerate_review' => 'nullable|in:Better,Worse,Same',
        ]);

        $feedbackData = $request->only(['response_id', 'feedback_type', 'context', 'regenerate_review']);

        $feedback = Feedback::create($feedbackData);

        return response()->json([
            'status' => 'success',
            'message' => 'Feedback Created Successfully',
            'data' => $feedback
        ], 201);
    }
}
