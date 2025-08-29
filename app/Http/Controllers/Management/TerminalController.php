<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\Terminal;
use App\Models\Logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TerminalController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:management.terminals.view')->only(['index']);
        $this->middleware('permission:management.terminals.create')->only(['store']);
        $this->middleware('permission:management.terminals.edit')->only(['update']);
        $this->middleware('permission:management.terminals.delete')->only(['delete']);
    }

    public function index()
    {
        $terminals = Terminal::orderBy('created_at', 'desc')->get();
        return view('admin.pages.management.terminals.index', compact('terminals'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $terminal = new Terminal();
            $terminal->name = $request->name;
            $terminal->address = $request->address;
            $terminal->notes = $request->notes;
            $terminal->entery_by_user = Auth::id();
            $terminal->save();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Create',
                'action_description' => "Terminal created: {$terminal->name}",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Terminal added successfully',
                'terminal' => $terminal
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add terminal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:terminals,id',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $terminal = Terminal::findOrFail($request->id);
            $terminal->name = $request->name;
            $terminal->address = $request->address;
            $terminal->notes = $request->notes;
            $terminal->save();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Update',
                'action_description' => "Terminal updated: {$terminal->name}",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Terminal updated successfully',
                'terminal' => $terminal
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update terminal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $terminal = Terminal::findOrFail($id);
            $name = $terminal->name;
            $terminal->delete();

            Logs::create([
                'user_id' => Auth::id(),
                'action_type' => 'Delete',
                'action_description' => "Terminal deleted: {$name}",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Terminal deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete terminal',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
