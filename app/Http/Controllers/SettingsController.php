<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * Display the settings page
     */
    public function index()
    {
        $settings = Setting::first();
        return view('admin.pages.settings', compact('settings'));
    }

    /**
     * Update the settings
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'login_email' => 'nullable|email|max:255',
            'facebook' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'soundcloud' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'info_phone' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'info_email' => 'nullable|email|max:255',
            'paypal_client_id' => 'nullable|string|max:255',
            'paypal_secret_key' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        try {
            DB::beginTransaction();

            // Update or create settings
            $settings = Setting::first();

            $settings->update([
                'name' => $request->name,
                'login_email' => $request->login_email,
                'facebook' => $request->facebook,
                'instagram' => $request->instagram,
                'info_phone' => $request->info_phone,
                'youtube' => $request->youtube,
                'info_email' => $request->info_email,
                'paypal_client_id' => $request->paypal_client_id,
                'paypal_secret_key' => $request->paypal_secret_key,
                'linkedin' => $request->linkedin,
                'soundcloud' => $request->soundcloud,
            ]);

            // If login_email and name are provided, update the user table as well
            if ($request->login_email && $request->name) {
                $user = User::where('id', Auth::id())->first();
                if ($user) {
                    $user->name = $request->name;
                    $user->email = $request->login_email;
                    $user->save();
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Settings updated successfully.']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        try {
            DB::beginTransaction();

            $user = User::where('id', Auth::id())->first();

            // Check if old password is correct
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json(['status' => 'error', 'message' => 'The old password is incorrect.']);
            }

            // Update password
            $user->password = Hash::make($request->new_password);
            $user->save();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Password changed successfully.']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }
}
