<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class NotificationController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('view notification'), 403);

        $notifications = Notification::latest()->paginate(perPage: 20);
        return view('admin.notification.index', compact('notifications'));
    }

    public function markAllRead()
    {
        Notification::latest()->where('is_seen', 0)->update(['is_seen' => 1]);
        return redirect()->back()->with('message', 'Updated Successfully.');
    }
}
