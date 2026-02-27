<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    protected $fillable = ['title', 'author', 'price', 'cover_image', 'category_id'];

    protected $appends = ['full_image_path'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    protected function fullImagePath(): Attribute
    {
        return new Attribute(
            get: fn () => Storage::url($this->cover_image),
        );
    }
}
