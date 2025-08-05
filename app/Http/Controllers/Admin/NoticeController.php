<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NoticeRequest;
use App\Models\Department;
use App\Models\Notice;
use App\Models\User;
use App\Services\DateService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Gate;


class NoticeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_unless(Gate::allows('view appnotice'), 403);

        $notices = Notice::latest()->paginate(perPage: 20);
        return view('admin.notice.index', compact('notices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_unless(Gate::allows('create appnotice'), 403);

        $departments = Department::where('status', 1)->oldest('order')->get();
        return view('admin.notice.create', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NoticeRequest $request)
    {
        abort_unless(Gate::allows('create appnotice'), 403);

        try {
            $input = $request->all();
            if (session('calendar') == 'BS') {
                $input['date'] = DateService::BSToAD($request->date);
            }

            $notice =  Notice::create($input);
            $notice->departments()->attach($request->departments);

            //get expoToken according to departments
            $expoTokens = $notice->expo_tokens;
            sendPushNotification($expoTokens, $request->title, $request->description);

            return redirect()->route('notices.index')->with('message', 'Notice Created Successfully');
        } catch (Exception $e) {
            return redirect()->route('notices.index')->with('warning', $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notice $notice)
    {
        abort_unless(Gate::allows('edit appnotice'), 403);

        $departments = Department::where('status', 1)->oldest('order')->get();
        return view('admin.notice.edit', compact('notice', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NoticeRequest $request, Notice $notice)
    {
        abort_unless(Gate::allows('edit appnotice'), 403);

        try {
            $notice->update($request->all());
            $notice->departments()->sync($request->departments);

            //get expoToken according to departments
            $expoTokens = $notice->expo_tokens;
            sendPushNotification($expoTokens, $request->title, $request->description);

            return redirect()->route('notices.index')->with('message', 'Update Successfully');
        } catch (Exception $e) {
            return redirect()->route('notices.index')->with('warning', $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notice $notice)
    {
        abort_unless(Gate::allows('delete appnotice'), 403);

        $notice->departments()->detach();
        $notice->delete();
        return redirect()->route('notices.index')->with('message', 'Delete Successfully');
    }

    public function sendPushNotification()
    {
        $employees = User::where('status', 'Active')
            ->where('user_type', 'Employee')
            ->with(['branch', 'department'])
            ->orderBy('first_name', 'ASC')
            ->get();

        $groupedEmployees = $employees->groupBy([
            function ($user) {
                return optional($user->branch)->name ?? 'Unknown Branch';
            },
            function ($user) {
                return optional($user->department)->name ?? 'Unknown Department';
            }
        ]);

        return view('admin.notice.individual', compact('groupedEmployees'));
    }

    public function sendPushNotificationToDevices(Request $request)
    {
        if ($request->expo_tokens) {
            $tokens = array_filter($request->expo_tokens);
            if (count($tokens) > 0) {
                $tokens = array_values($tokens);
                sendPushNotification($tokens, $request->title, $request->description);
                return redirect()->back()->with('success', 'Push Notification Sent Successfully');
            } else {
                return redirect()->back()->with('warning', 'Selected employee has not enabled notification.');
            }
        }
        return redirect()->back()->with('warning', 'No Employees selected');
    }
}
