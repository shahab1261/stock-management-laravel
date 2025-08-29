<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:logs.view')->only(['index', 'getRecentLogs']);
    }

    public function index()
    {
        $logs = Logs::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.pages.logs.index', compact('logs'));
    }

    public function getRecentLogs()
    {
        return Logs::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }
}
