<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::latest()->paginate(5);
        return Inertia::render('properties/index', [
            'properties' => $properties,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'location' => 'required',
            'price' => 'required',
            'description' => 'nullable',
            'image' => 'nullable',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('public/images');
        }

        Property::create($data);

        return redirect()->back()->with('success', 'Property created successfully!');
    }

    public function update(Request $request, Property $property)
    {
        $data = $request->validate([
            'title' => 'required',
            'location' => 'required',
            'price' => 'required',
            'description' => 'nullable',
            'image' => 'nullable',
        ]);

        if ($request->hasFile('image')) {

            if ($property->image && Storage::disk('public')->exists($property->image)) {
                Storage::disk('public')->delete($property->image);
            }
            $data['image'] = $request->file('image')->store('public/images');
        }

        $property->update($data);

        return redirect()->route('properties.index')->with('success', 'Property updated successfully!');
    }

    public function destroy(Property $property)
    {
        if ($property->image && Storage::disk('public')->exists($property->image)) {
            Storage::disk('public')->delete($property->image);
        }
        $property->delete();

        return redirect()->route('properties.index')->with('success', 'Property deleted successfully!');
    }
}
