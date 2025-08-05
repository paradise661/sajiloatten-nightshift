<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $feedback = Feedback::create([
            'user_id' => $request->user()->id,
            'message' => $request->message,
            'status' => 'Pending',
            'is_seen' => false,
        ]);

        return response()->json([
            'message' => 'Feedback submitted successfully.',
            'data' => $feedback
        ], 201);
    }

    // Fetch feedback (admin or your own)
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Feedback::with('employee');

        if (!($user->hasRole('ADMIN') || $user->hasRole('SENIOR-ADMIN'))) {
            $query->where('user_id', $user->id);
        }

        $feedbacks = $query->latest()->get();
        $feedbacks->transform(function ($feedback) {
            $feedback->submitted_at = Carbon::parse($feedback->created_at)->diffForHumans();
            return $feedback;
        });

        return response()->json([
            'message' => 'Feedback list fetched successfully.',
            'data' => $feedbacks,
        ]);
    }
}
