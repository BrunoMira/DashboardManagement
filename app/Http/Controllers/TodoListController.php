<?php

namespace App\Http\Controllers;

use App\Models\TodoList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:32',
        ]);

        TodoList::create($data);

        return redirect()->route('lists.index');
    }

    public function update(Request $request, TodoList $list): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:32',
        ]);

        $list->update($data);

        return redirect()->route('lists.index');
    }

    public function destroy(TodoList $list): RedirectResponse
    {
        $list->delete();

        return redirect()->route('lists.index');
    }
}
