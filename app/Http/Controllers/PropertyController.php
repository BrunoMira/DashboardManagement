<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Property;
use App\Services\PropertyService;
use Inertia\Inertia;

class PropertyController extends Controller
{
    private PropertyService $propertyService;

    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
    }

    public function index()
    {
        $properties = Property::latest()->paginate(5);

        return Inertia::render('properties/index', [
            'properties' => $properties,
        ]);
    }

    public function store(StorePropertyRequest $request)
    {
        $this->propertyService->create($request->validated(), $request->file('image'));

        return redirect()->back()->with('success', 'Property created successfully!');
    }

    public function update(UpdatePropertyRequest $request, Property $property)
    {
        $this->propertyService->update($request->validated(), $request->file('image'), $property);

        return redirect()->route('properties.index')->with('success', 'Property updated successfully!');
    }

    public function destroy(Property $property)
    {
        $this->propertyService->delete($property);

        return redirect()->route('properties.index')->with('success', 'Property deleted successfully!');
    }
}
