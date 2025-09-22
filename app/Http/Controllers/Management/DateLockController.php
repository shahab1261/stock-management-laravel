<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Logs;

class DateLockController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:management.date-lock.view')->only(['index']);
        $this->middleware('permission:management.date-lock.edit')->only(['update']);
    }

    /**
     * Display the date lock page
     */
    public function index()
    {
        $settings = Settings::first();
        $hasSystemLockedPermission = Auth::user()->can('system_locked');
        return view('admin.pages.management.date-lock.index', compact('settings', 'hasSystemLockedPermission'));
    }

    /**
     * Update the date lock
     */
    public function update(Request $request)
    {
        $hasSystemLockedPermission = Auth::user()->can('system_locked');

        $validator = Validator::make($request->all(), [
            'date_lock' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        // Check if user can set date lock to past/future dates
        if (!$hasSystemLockedPermission) {
            $today = now()->format('Y-m-d');
            $currentSettings = Settings::first();
            $currentDateLock = $currentSettings ? $currentSettings->date_lock : $today;

            // Allow only current system locked date or today's date
            if ($request->date_lock !== $today && $request->date_lock !== $currentDateLock) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You can only set the date lock to either today\'s date or the current system locked date.'
                ]);
            }
        }

        try {
            DB::beginTransaction();

            // Get or create settings
            $settings = Settings::firstOrNew(['id' => 1]);
            $oldDateLock = $settings->date_lock;

            // Update date lock
            $settings->date_lock = $request->date_lock;
            $settings->save();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Update',
                'action_description' => "Date lock updated from {$oldDateLock} to {$settings->date_lock}",
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Date lock updated successfully.']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }
}
