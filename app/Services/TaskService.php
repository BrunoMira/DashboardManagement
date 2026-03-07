<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getPaginatedTasks(array $filters = []): LengthAwarePaginator
    {
        $query = Task::query()->with('list');

        $search = $filters['search'] ?? null;
        $priority = $filters['priority'] ?? null;
        $list_id = $filters['list_id'] ?? null;

        $query->when($search, function ($query, $search) {
            $query->whereSearch($search);
        });

        $query->when($priority, function ($query, $priority) {
            $query->where('priority', $priority);
        });

        $query->when($list_id, function ($query, $list_id) {
            $query->where('list_id', $list_id);
        });

        return $query->latest()->paginate(10);
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(array $data, Task $task): Task
    {
        $task->update($data);

        return $task;
    }

    public function delete(Task $task): bool
    {
        return $task->delete();
    }
}
