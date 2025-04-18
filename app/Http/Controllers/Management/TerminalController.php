<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Management\Terminal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TerminalController extends Controller
{
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
            $terminal->delete();

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
