<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Logs;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:management.settings.view')->only(['index']);
        $this->middleware('permission:management.settings.edit')->only(['update']);
    }
    /**
     * Display the settings page
     */
    public function index()
    {
        $settings = Settings::first();
        return view('admin.pages.management.settings.index', compact('settings'));
    }

    /**
     * Update the settings
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'short_desc' => 'nullable|string|max:255',
            'logo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        try {
            DB::beginTransaction();

            // Get or create settings
            $settings = Settings::firstOrNew(['id' => 1]);

            // Handle logo upload if provided
            if ($request->hasFile('logo_path')) {
                // Delete old logo if exists
                if ($settings->logo_path && Storage::exists('public/' . $settings->logo_path)) {
                    Storage::delete('public/' . $settings->logo_path);
                }

                // Upload new logo
                $logoPath = $request->file('logo_path')->store('logos', 'public');
                $settings->logo_path = 'storage/' . $logoPath;
            }

            // Update other fields
            $settings->company_name = $request->company_name;
            $settings->short_desc = $request->short_desc;
            $settings->save();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Update',
                'action_description' => "Settings updated: {$settings->company_name}",
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Settings updated successfully.']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }
}
