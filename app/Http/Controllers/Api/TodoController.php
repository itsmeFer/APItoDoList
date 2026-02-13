<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{
    public function index(Request $request)
    {
        $query = Todo::where('user_id', $request->user()->id);

        if ($request->has('category')) {
            $query->byCategory($request->category);
        }
        if ($request->has('status')) {
            if ($request->status === 'Active') $query->active();
            if ($request->status === 'Completed') $query->completed();
        }
        if ($request->has('search')) {
            $query->search($request->search);
        }

        $todos = $query->orderByRaw("
            CASE WHEN is_completed = 1 THEN 1 ELSE 0 END,
            CASE priority WHEN 'High' THEN 1 WHEN 'Medium' THEN 2 WHEN 'Low' THEN 3 END
        ")->get();

        return response()->json(['success' => true, 'data' => $todos]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'category' => 'required|in:Personal,Work,Shopping,Health,Learning',
            'priority' => 'required|in:High,Medium,Low',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $todo = Todo::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'priority' => $request->priority,
            'due_date' => $request->due_date,
        ]);

        return response()->json(['success' => true, 'data' => $todo], 201);
    }

    public function update(Request $request, $id)
    {
        $todo = Todo::where('user_id', $request->user()->id)->findOrFail($id);
        $todo->update($request->all());
        return response()->json(['success' => true, 'data' => $todo]);
    }

    public function toggle(Request $request, $id)
    {
        $todo = Todo::where('user_id', $request->user()->id)->findOrFail($id);
        $todo->update(['is_completed' => !$todo->is_completed]);
        return response()->json(['success' => true, 'data' => $todo]);
    }

    public function destroy(Request $request, $id)
    {
        Todo::where('user_id', $request->user()->id)->findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Deleted']);
    }

    public function statistics(Request $request)
    {
        $user = $request->user();
        $stats = [
            'total' => Todo::where('user_id', $user->id)->count(),
            'active' => Todo::where('user_id', $user->id)->active()->count(),
            'completed' => Todo::where('user_id', $user->id)->completed()->count(),
            'overdue' => Todo::where('user_id', $user->id)->overdue()->count(),
        ];
        return response()->json(['success' => true, 'data' => $stats]);
    }
}