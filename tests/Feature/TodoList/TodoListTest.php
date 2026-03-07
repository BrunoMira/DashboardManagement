<?php

namespace Tests\Feature\TodoList;

use App\Models\User;
use Database\Factories\TodoListFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_get_index_todo_lists(): void
    {
        $user = User::factory()->create();
        $lists = TodoListFactory::new()->times(3)->create();
        $response = $this->actingAs($user)->get(route('lists.index'));

        $response->assertOk();
        $response->assertSessionHasNoErrors();
        $response->assertInertia(function ($page) use ($lists) {
            $page->component('lists/Index')
                ->has('lists', $lists->count());
        });
    }

    public function test_create_todo_list(): void
    {
        $user = User::factory()->create();
        $list = TodoListFactory::new()->make();
        $response = $this->actingAs($user)->followingRedirects()->post(route('lists.store'), [
            'name' => $list->name,
            'color' => $list->color,
        ]);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('lists', [
            'name' => $list->name,
            'color' => $list->color,
        ]);

        $response->assertInertia(function ($page) {
            $page->component('lists/Index')
                ->has('lists', 1);
        });
    }

    public function test_without_form_create_todo_list(): void
    {
        $user = User::factory()->create();
        $fields = ['name', 'color'];
        $data = array_fill_keys($fields, '');

        $response = $this->actingAs($user)->post(route('lists.store'), $data);
        $errors = session('errors')->getBag('default')->keys();

        $this->assertEqualsCanonicalizing(
            $fields,
            $errors
        );

        $response->assertInvalid($fields);
    }

    public function test_update_todo_list(): void
    {
        $user = User::factory()->create();
        $list = TodoListFactory::new()->create();
        $updateList = TodoListFactory::new()->make();

        $response = $this->actingAs($user)->followingRedirects()->put(route('lists.update', $list), [
            'name' => $updateList->name,
            'color' => $updateList->color,
        ]);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('lists', [
            'name' => $updateList->name,
            'color' => $updateList->color,
        ]);

        $response->assertInertia(function ($page) {
            $page->component('lists/Index')
                ->has('lists', 1);
        });
    }

    public function test_without_form_update_todo_list(): void
    {
        $user = User::factory()->create();
        $list = TodoListFactory::new()->create();
        $fields = ['name', 'color'];
        $data = array_fill_keys($fields, '');

        $response = $this->actingAs($user)->put(route('lists.update', $list->id), $data);

        $errors = session('errors')->getBag('default')->keys();

        $this->assertEqualsCanonicalizing(
            $fields,
            $errors
        );

        $response->assertInvalid($fields);
    }

    public function test_delete_todo_list(): void
    {
        $user = User::factory()->create();
        $list = TodoListFactory::new()->create();
        TodoListFactory::new()->create();

        $response = $this->actingAs($user)->followingRedirects()->delete(route('lists.destroy', $list));
        $this->assertDatabaseMissing('lists', [
            'id' => $list->id,
        ]);

        $response->assertInertia(function ($page) {
            $page->component('lists/Index')
                ->has('lists', 1);
        });
    }
}
