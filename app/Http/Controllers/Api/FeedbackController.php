<?php

namespace App\Http\Controllers\Api;

use App\Models\Feedback;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        DB::beginTransaction();

        try {
            // Fetch the response by its ID

            $response_id = $request->input('response_id');
            $response = response::find($response_id);

            // Update the response status to 'Dislike'
            if ($response->response_status !== 'Dislike') {
                $response->update(['response_status' => 'Dislike']);
                $response->save();
            }

            // Prepare feedback data
            $feedbackData = $request->only(['response_id', 'feedback_type', 'context', 'regenerate_review']);

            // Create new feedback
            $feedback = Feedback::create($feedbackData);

            DB::commit();

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => 'Feedback Created Successfully',
                'data' => $feedback
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create feedback',
                'error' => $e->getMessage()
            ], 500);
        }
    }




    public function review(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'response_id' => 'required|exists:responses,id',
            'feedback_type' => 'required|in:dislike,regenerate',
            'regenerate_review' => 'nullable|in:Better,Worse,Same',
        ]);

        DB::beginTransaction();

        try {
            // Fetch the response by its ID
            $response_id = $request->input('response_id');
            $response = Response::find($response_id);

            // Prepare feedback data
            $feedbackData = $request->only(['response_id', 'feedback_type', 'regenerate_review']);

            // Create a new feedback record
            $feedback = Feedback::create($feedbackData);

            DB::commit();

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => 'Feedback Created Successfully',
                'data' => $feedback
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create feedback',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
