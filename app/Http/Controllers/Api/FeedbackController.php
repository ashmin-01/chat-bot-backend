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

        $feedbackData = $request->only(['response_id', 'feedback_type']);
        if ($request->feedback_type === 'dislike') {
            // Update response status to 'dislike'
            $feedbackData['response_status'] = 'Dislike';
            // Fill the context field if provided
            $feedbackData['context'] = $request->input('context');
        } elseif ($request->feedback_type === 'regenerate') {
            // Fill the regenerate review field if provided
            $feedbackData['regenerate_review'] = $request->input('regenerate_review');
        }

        $feedback = Feedback::create($feedbackData);

        return response()->json([
            'status' => 'success',
            'message' => 'Feedback Created Successfully',
            'data' => $feedback
        ], 201);
    }

}
