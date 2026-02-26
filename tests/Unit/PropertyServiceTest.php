<?php

namespace Tests\Unit;

use App\Models\Property;
use App\Services\PropertyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PropertyServiceTest extends TestCase
{
    use RefreshDatabase;
    private PropertyService $service;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake();
        $this->service = app(PropertyService::class);
    }

    /**
     * A basic feature test example.
     */
    function test_create_property_with_image(): void
    {
        $file = $this->createFakeImage('house.jpg');

        $data = $this->returnData();

        $property = $this->service->create($data, $file);

        $this->assertInstanceOf(Property::class, $property);

        $this->assertPropertyMatches($data, $property);

        $this->assertModelExists($property);

        $this->assertStringStartsWith('properties/', $property->image);
        Storage::disk()->assertExists($property->image);
    }

    function test_create_property_without_image(): void
    {
        $data = $this->returnData();

        $property = $this->service->create($data, null);

        $this->assertInstanceOf(Property::class, $property);

        $this->assertPropertyMatches($data, $property);

        $this->assertModelExists($property);

        Storage::disk()->assertDirectoryEmpty('/properties');
    }

    function test_it_updates_property_and_replaces_old_image(): void
    {

        $file = $this->createFakeImage('house.jpg');

        $data = $this->returnData();

        $property = $this->service->create($data, $file);

        $oldImage = $property->image;

        $this->assertStringStartsWith('properties/', $oldImage);
        Storage::disk()->assertExists($oldImage);

        $file = $this->createFakeImage('house2.jpg');

        $updatedData = [
            'title' => 'House 123',
            'location' => '123 Main St 123',
            'price' => 10,
            'description' => 'A beautiful 123 house',
        ];

        $property = $this->service->update($updatedData, $file, $property);

        $this->assertInstanceOf(Property::class, $property);

        $this->assertPropertyMatches($updatedData, $property);

        $this->assertModelExists($property);

        $this->assertStringStartsWith('properties/', $property->image);
        Storage::disk()->assertExists($property->image);
        Storage::disk()->assertMissing($oldImage);
    }

    function test_update_property_without_image(): void
    {
        $data = $this->returnData();

        $property = Property::create($data);

        $updatedData = $data = $this->returnData([
            'title' => 'House 123',
            'location' => '123 Main St 123',
            'price' => 10,
            'description' => 'A beautiful 123 house',
            'image' => null,
        ]);

        $property = $this->service->update($updatedData, null, $property);

        $this->assertInstanceOf(Property::class, $property);

        $this->assertPropertyMatches($updatedData, $property);

        $this->assertModelExists($property);

        Storage::disk()->assertDirectoryEmpty('/properties');
    }

    function test_it_deletes_property_and_removes_image(): void
    {

        $file = $this->createFakeImage('house.jpg');

        $data = $this->returnData();

        $property = Property::create($data);

        $this->service->delete($property);

        $this->assertDatabaseMissing('properties', [
            'id' => $property->id,
        ]);

        $this->assertModelMissing($property);

        Storage::disk()->assertDirectoryEmpty('/properties');
    }

    private function returnData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'House',
            'location' => '123 Main St',
            'price' => 1000,
            'description' => 'A beautiful house',
        ], $overrides);
    }

    private function assertPropertyMatches(array $data, Property $property): void
    {
        $this->assertEquals($data['title'], $property->title);
        $this->assertEquals($data['location'], $property->location);
        $this->assertEquals($data['price'], $property->price);
        $this->assertEquals($data['description'], $property->description);
        if(isset($data['image'])) {
            $this->assertEquals($data['image'], $property->image);
        }
    }

    private function createFakeImage(string $name): UploadedFile
    {
        return UploadedFile::fake()->image($name);
    }
}
