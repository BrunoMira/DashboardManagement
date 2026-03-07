<?php

namespace App\Http\Controllers;

use App\Http\Requests\TodoListRequest;
use App\Models\TodoList;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class TodoListController extends Controller
{
    public function index()
    {
        $lists = TodoList::withCount('tasks')->latest()->get();

        return Inertia::render('lists/Index', [
            'lists' => $lists,
        ]);
    }

    public function store(TodoListRequest $request): RedirectResponse
    {
        $data = $request->validated();
        TodoList::create($data);

        return redirect()->route('lists.index');
    }

    public function update(TodoListRequest $request, TodoList $list): RedirectResponse
    {
        $data = $request->validated();
        $list->update($data);

        return redirect()->route('lists.index');
    }

    public function destroy(TodoList $list): RedirectResponse
    {
        $list->delete();

        return redirect()->route('lists.index');
    }
}
