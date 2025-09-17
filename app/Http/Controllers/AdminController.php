<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function __construct()
    {
        // Dashboard should be accessible to all logged-in users
        // $this->middleware('permission:dashboard.view')->only('dashboard');
    }

    public function dashboard()
    {   Log::info('Admin Dashboard');
        return view('admin.pages.dashboard');
    }

    public function login(Request $request)
    {
        try {
            $input = $request->isJson() ? $request->json()->all() : $request->all();

            $validator = Validator::make($input, [
                'email' => ['required', 'string', 'email', 'max:255'],
                'password' => ['required', 'string', 'min:8'],
            ]);

            if ($validator->fails()) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
                }
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $remember = $request->filled('remember');

            $candidate = \App\Models\User::where('email', $input['email'])->first();
            if ($candidate && empty($candidate->password)) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Login not allowed for this account.'], 403);
                }
                return redirect()->back()->with('error', 'Login not allowed for this account.')->withInput();
            }
            // dd($candidate);

            if ($candidate->status == '0') {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Your account is blocked, please contact admin.'], 403);
                }
                return redirect()->back()->with('error', 'Your account is blocked, please contact admin.')->withInput();
            }

            if (Auth::attempt(['email' => $input['email'], 'password' => $input['password']], $remember)) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => true, 'message' => 'Login successful']);
                }
                return redirect()->route('admin.dashboard');
            }

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Invalid credentials']);
            }
            return redirect()->back()->with('error', 'Invalid credentials')->withInput();
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Something went wrong: ' . $e->getMessage()]);
            }
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.')->withInput();
        }
    }

    public function create(){
        return view('admin.pages.ad_product');
    }

    public function logout()
    {
        // dd('logout');
        Auth::logout();

        return redirect()->route('admin.login');
    }
}
