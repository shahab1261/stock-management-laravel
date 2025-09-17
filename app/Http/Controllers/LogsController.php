<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class LogsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:logs.view')->only(['index', 'getRecentLogs', 'getData']);
    }

    public function index()
    {
        // Get all users for the filter dropdown
        $users = User::select('id', 'name')->orderBy('name')->get();

        return view('admin.pages.logs.index', compact('users'));
    }

    public function getData(Request $request)
    {
        $query = Logs::with('user:id,name')
            ->select('logs.*');

        // Apply filters
        if ($request->filled('user_filter') && $request->user_filter != '') {
            $query->where('user_id', $request->user_filter);
        }

        if ($request->filled('action_filter') && $request->action_filter != '') {
            $query->where('action_type', $request->action_filter);
        }

        if ($request->filled('date_from') && $request->date_from != '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to') && $request->date_to != '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return DataTables::of($query)
            ->addColumn('user_info', function ($log) {
                $userInitial = $log->user ? substr($log->user->name, 0, 1) : '?';
                $userName = $log->user ? $log->user->name : 'Unknown User';

                return [
                    'initial' => strtoupper($userInitial),
                    'name' => $userName
                ];
            })
            ->addColumn('action_badge', function ($log) {
                $badgeClass = match($log->action_type) {
                    'Create' => 'success',
                    'Update' => 'primary',
                    'Delete' => 'danger',
                    default => 'info'
                };

                $icon = match($log->action_type) {
                    'Create' => 'plus',
                    'Update' => 'pencil',
                    'Delete' => 'trash',
                    default => 'info-circle'
                };

                return [
                    'class' => $badgeClass,
                    'icon' => $icon,
                    'text' => ucfirst($log->action_type)
                ];
            })
            ->addColumn('formatted_date', function ($log) {
                return [
                    'date' => $log->created_at->format('Y-m-d'),
                    'time' => $log->created_at->format('H:i:s')
                ];
            })
            ->filterColumn('user_info', function ($query, $keyword) {
                $query->whereHas('user', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->orderColumn('user_info', function ($query, $order) {
                $query->join('users', 'logs.user_id', '=', 'users.id')
                      ->orderBy('users.name', $order);
            })
            ->rawColumns([])
            ->make(true);
    }

    public function getRecentLogs()
    {
        return Logs::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }
}
