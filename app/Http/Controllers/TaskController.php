<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Task;
use App\Models\TodoList;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{

    private TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'priority', 'list_id']);
        $tasks = $this->taskService->getPaginatedTasks($filters);

        $lists = TodoList::get();

        return Inertia::render('tasks/Index', [
            'tasks' => $tasks,
            'lists' => $lists,
            'filters' => $filters,
        ]);
    }

    public function store(StoreTaskRequest $request)
    {
        $data = $request->validated();

        $this->taskService->create($data);

        return redirect()->back();
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $data = $request->validated();
        $this->taskService->update($data, $task);

        return redirect()->back();
    }

    public function destroy(Request $request, Task $task)
    {
        $this->taskService->delete($task);
        return redirect()->back();
    }
}