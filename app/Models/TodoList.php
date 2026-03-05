<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TodoList extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'color'];

    protected $table = 'lists';

    public function tasks()
    {
        return $this->hasMany(Task::class, 'list_id');
    }
}
