<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TodoList;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index(Request $request)
    {

        $query = Task::query()->with('list');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('list_id')) {
            $query->where('list_id', $request->list_id);
        }

        $tasks = $query->latest()->paginate(10)->withQueryString();

        $lists = TodoList::get();

        return Inertia::render('tasks/Index', [
            'tasks' => $tasks,
            'lists' => $lists,
            'filters' => $request->only(['search', 'priority', 'list_id']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'description' => 'sometimes',
            'priority' => 'required|in:low,normal,high',
            'is_complete' => 'sometimes|boolean',
            'list_id' => 'required',
        ]);

        $data['priority'] = $data['priority'] ?? 'normal';

        Task::create($data);

        return redirect()->back();
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'title' => 'required',
            'description' => 'sometimes',
            'priority' => 'required|in:low,normal,high',
            'is_complete' => 'sometimes|boolean',
        ]);

        $data['priority'] = $data['priority'] ?? 'normal';

        $task->update($data);

        return redirect()->back();
    }

    public function destroy(Request $request, Task $task)
    {
        $task->delete();

        return redirect()->back();
    }
}
