<?php

namespace App\Observers;

use App\Models\Property;
use Illuminate\Support\Facades\Storage;

class PropertyObserver
{
    public function deleted(Property $property)
    {
        if ($property->image && Storage::disk()->exists($property->image)) {
            return Storage::disk()->delete($property->image);
        }
    }
}
