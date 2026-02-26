<?php

namespace App\Observers;

use App\Models\Property;
use Illuminate\Support\Facades\Storage;

class PropertyObserver
{
    public function updating(Property $property)
    {
        if ($property->image && $property->isDirty('image') && Storage::disk()->exists($property->getOriginal('image'))) {
            Storage::disk()->delete($property->getOriginal('image'));
        }
    }

    public function deleted(Property $property)
    {
        if ($property->image && Storage::disk()->exists($property->image)) {
            return Storage::disk()->delete($property->image);
        }
    }
}
