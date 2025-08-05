<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountDeletion;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AccountDeletionController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('view accountdeletion'), 403);
        $accounts = AccountDeletion::latest()->paginate(perPage: 20);
        return view('admin.account-deletion.index', compact('accounts'));
    }

    public function update(Request $request, AccountDeletion $account)
    {
        abort_unless(Gate::allows('view accountdeletion'), 403);

        try {
            $account->update([
                'action_by' => Auth::id(),
            ]);

            User::where('id', $account->user_id)->update(['status' => 'Deleted']);

            return redirect()->route('accountdeletion')->with('message', 'Request Updated Successfully.');
        } catch (Exception $e) {
            return redirect()->route('accountdeletion')->with('warning', $e->getMessage())->withInput();
        }
    }
}
