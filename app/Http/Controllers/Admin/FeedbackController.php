<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FeedbackController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('view feedback'), 403);

        $feedbacks = Feedback::latest()->paginate(perPage: 20);
        return view('admin.feedback.index', compact('feedbacks'));
    }

    public function destroy(Feedback $feedback)
    {
        abort_unless(Gate::allows('delete feedback'), 403);

        $feedback->delete();
        return redirect()->route('feedbacks.index')->with('message', 'Delete Successfully');
    }
}
