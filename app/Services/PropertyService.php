<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Http\UploadedFile;

class PropertyService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function create(array $data, ?UploadedFile $file): Property
    {
        if ($file) {
            $data['image'] = $this->saveImage($file);
        }

        return Property::create($data);
    }

    public function update(array $data, ?UploadedFile $file, Property $property): Property
    {
        if ($file) {
            $data['image'] = $this->saveImage($file);
        }

        $property->update($data);
        return $property;
    }

    public function delete(Property $property): bool
    {
        return $property->delete();
    }

    private function saveImage(UploadedFile $file): string | null
    {
        if ($file) {
            $path = $file->store('properties');
            return $path ?? null;
        }

        return null;
    }
}
