<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\SendOTPToEmployee;
use App\Models\AccountDeletion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use File;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;


class UserAuthController extends Controller
{
    /**
     * Return a standardized success response
     */
    private function respondWithSuccess($message, $data = [])
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], 200);
    }

    /**
     * Return a standardized error response
     */
    private function respondWithError($message, $errors = [], $statusCode = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError('Validation failed', $validator->errors(), 422);
        }
        $loginInput = $request->email;

        $user = User::with(['department', 'branch', 'shift'])
            ->where(function ($query) use ($loginInput) {
                if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
                    $query->where('email', $loginInput);
                } elseif (preg_match('/^\d{10,15}$/', $loginInput)) {
                    $query->where('phone', $loginInput);
                }
            })
            ->first();

        if (!$user || !Hash::check(trim($request->password), $user->password)) {
            return $this->respondWithError('The provided credentials are incorrect.', [
                'email' => ['The provided credentials are incorrect.']
            ], 401);
        }

        // Check if user status is active
        if ($user->status == 'Deleted') {
            $message = 'Email not found in our records';
            return $this->respondWithError($message, [], 403);
        }

        if ($user->status !== 'Active') {
            $message = 'Your account is ' . strtolower($user->status) . '. Please contact the administrator for further assistance.';
            return $this->respondWithError($message, [], 403);
        }

        // Update expo_token only if provided and different
        // if ($request->has('expo_token') && $request->expo_token !== $user->expo_token) {
        // }
        if ($user->device_ids) {
            if (!$user->device_flexible) {
                if (json_decode($user->device_ids) != $request->deviceId) {
                    $message = 'Unauthorized device for this email.';
                    return $this->respondWithError($message, [], 403);
                }
            }
        }

        $user->device_ids = $request->deviceId ? json_encode($request->deviceId) : null;
        $user->platform = $request->platform;
        $user->save();

        $token = $user->createToken('MyAppToken')->plainTextToken;
        $user->profile_name = strtoupper(string: ucfirst($user->first_name[0] ?? '')) . strtoupper(ucfirst($user->last_name[0] ?? ''));

        return $this->respondWithSuccess('Login successful', [
            'access_token' => $token,
            'user' => $user
        ]);
    }

    public function biometricLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError('Validation failed', $validator->errors(), 422);
        }

        $user = User::with(['department', 'branch', 'shift'])->where('email', $request->email)->first();

        if (!$user) {
            return $this->respondWithError('The provided credentials are incorrect.', [
                'email' => ['The provided credentials are incorrect.']
            ], 401);
        }

        // Check if user status is active
        if ($user->status == 'Deleted') {
            $message = 'Email not found in our records';
            return $this->respondWithError($message, [], 403);
        }

        if ($user->status !== 'Active') {
            $message = 'Your account is ' . strtolower($user->status) . '. Please contact the administrator for further assistance.';
            return $this->respondWithError($message, [], 403);
        }

        // Update expo_token only if provided and different
        // if ($request->has('expo_token') && $request->expo_token !== $user->expo_token) {
        // }
        if ($user->device_ids) {
            if (!$user->device_flexible) {
                if (json_decode($user->device_ids) != $request->deviceId) {
                    $message = 'Unauthorized device for this email.';
                    return $this->respondWithError($message, [], 403);
                }
            }
        }

        $user->device_ids = $request->deviceId ? json_encode($request->deviceId) : null;
        $user->platform = $request->platform;
        $user->save();

        $token = $user->createToken('MyAppToken')->plainTextToken;
        $user->profile_name = strtoupper(string: ucfirst($user->first_name[0] ?? '')) . strtoupper(ucfirst($user->last_name[0] ?? ''));

        return $this->respondWithSuccess('Login successful', [
            'access_token' => $token,
            'user' => $user,
        ]);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError('Validation failed', $validator->errors(), 422);
        }

        $user = $request->user(); // Assuming you are using Sanctum or similar for authentication

        if (!$user || !Hash::check($request->current_password, $user->password)) {
            return $this->respondWithError('Current password is incorrect.', [
                'current_password' => ['The current password is incorrect.']
            ], 401);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return $this->respondWithSuccess('Password changed successfully');
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if ($request->hasFile('image')) {
            if ($user->image) {
                $this->removeFile($user->image);
            }

            $user->image = $this->fileUpload($request, 'image');
        }

        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }

    public function getProfile(Request $request)
    {
        $profile = User::with(['branch', 'department'])->where('email', $request->user()->email)->first();

        return response()->json([
            'success' => true,
            'message' => 'Profile retrieved successfully.',
            'user' => $profile,
        ]);
    }

    public function deleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expo_token' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError('Validation failed', $validator->errors(), 422);
        }

        $user = $request->user();

        AccountDeletion::create([
            'user_id' => $user->id ?? NULL,
            'user' => $user ?? NULL
        ]);

        $user->status = 'Deleted';
        $user->save();

        return $this->respondWithSuccess('Your account has been successfully deleted.');
    }

    public function fileUpload(Request $request, $name)
    {
        $imageName = '';

        if ($image = $request->file($name)) {
            $destinationPath = public_path('/uploads/employee');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $imageName = date('YmdHis') . $name . "-" . $image->getClientOriginalName();
            $fullImagePath = $destinationPath . '/' . $imageName;

            $manager = new ImageManager(new GdDriver());

            $manager->read($image->getRealPath())
                ->resize(500, 500, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->save($fullImagePath);

            return $imageName;
        }

        return $imageName;
    }


    public function removeFile($file)
    {
        if ($file) {
            $filePath = str_replace(asset(''), '', $file);

            if ($filePath === 'assets/images/profile.jpg') {
                return;
            }

            $path = public_path($filePath);

            if (File::exists($path)) {
                File::delete($path);
            }
        }
    }

    public function forgotPasswordOtp(Request $request)
    {
        $user = User::where('email', $request->email)->where('user_type', 'Employee')->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Email Not Found',
            ], 422);
        }

        $otp = rand(10000, 99999);
        $user->update(['otp' => $otp]);

        //send mail to employee
        Mail::to($request->email ?? "")->send(
            new SendOTPToEmployee($user)
        );

        return response()->json([
            'success' => true,
            'message' => 'OTP has been sent to your mail',
        ]);
    }

    public function forgotPasswordCheckOtp(Request $request)
    {
        $user = User::where('email', $request->email)->where('otp', $request->otp)->where('user_type', 'Employee')->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid OTP',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP Matched',
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'otp' => 'required',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError('Validation failed', $validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->where('otp', $request->otp)->where('user_type', 'Employee')->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid Data',
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->otp = null;
        $user->save();

        return $this->respondWithSuccess('Password reset successfully');
    }

    public function savePushToken(Request $request)
    {
        $user = $request->user();

        if ($request->status == 'add') {
            $user->expo_token = $request->expo_token;
        } else {
            $user->expo_token = null;
        }

        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'Expo Token Updated',
            'user' => $user,
        ]);
    }

    public function changePasswordForFirstOpen(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError('Validation failed', $validator->errors(), 422);
        }

        $user = $request->user();

        $user->password = Hash::make($request->new_password);
        $user->remember_token = now();
        $user->save();

        return $this->respondWithSuccess('Password changed successfully');
    }
}
