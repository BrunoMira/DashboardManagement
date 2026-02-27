<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with('category')->paginate(5);
        $categories = Category::all();

        return Inertia::render('books/index', [
            'books' => $books,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'price' => 'required|numeric',
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('cover_images');
        }

        $book = Book::create($data);

        return redirect()->back()->with('success', 'Book criada com sucesso!');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'price' => 'required|numeric',
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
            'category_id' => 'required|exists:categories,id',
        ]);

        $book = Book::findOrFail($id);

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk()->delete($book->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('cover_images');
        }

        $book->update($data);

        return redirect()->back()->with('success', 'Livro atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        if($book->cover_image) {
            Storage::disk()->delete($book->cover_image);
        }

        $book->delete();

        return redirect()->back()->with('success', 'Livro excluída com sucesso!');
    }
}
