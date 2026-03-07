<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Services\TaskService;
use Database\Factories\TodoListFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
    use RefreshDatabase;

    private TaskService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(TaskService::class);
    }

    public function test_get_paginated_tasks(): void
    {
        $list = TodoListFactory::new()->create();
        $list2 = TodoListFactory::new()->create();

        $task = $this->createTask([
            'title' => 'House 123',
            'description' => 'A beautiful 123 apartment',
            'priority' => 'normal',
            'list_id' => $list->id,
            'created_at' => now()->subMinutes(3),
        ]);

        $task2 = $this->createTask([
            'title' => 'Tree 123',
            'description' => 'The house 123',
            'priority' => 'high',
            'list_id' => $list2->id,
            'created_at' => now()->subMinutes(2),
        ]);

        $task3 = $this->createTask([
            'title' => 'Country 456',
            'description' => 'An awesome country',
            'priority' => 'low',
        ]);

        $tasks = $this->service->getPaginatedTasks();

        //  validate latest sorting
        $this->assertEquals($task3->id, $tasks->first()->id);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertInstanceOf(Task::class, $task2);
        $this->assertInstanceOf(Task::class, $task3);
        $this->assertModelExists($task);
        $this->assertModelExists($task2);
        $this->assertModelExists($task3);

        $filters = [
            'search' => '123',
        ];

        $tasks = $this->service->getPaginatedTasks($filters);
        $this->assertCount(2, $tasks->items());

        $filters = [
            'priority' => 'high',
        ];

        $tasks = $this->service->getPaginatedTasks($filters);
        $this->assertCount(1, $tasks->items());

        $filters = [
            'list_id' => $list->id,
        ];

        $tasks = $this->service->getPaginatedTasks($filters);
        $this->assertCount(1, $tasks->items());

        $filters = [
            'search' => 'house',
            'priority' => 'high',
            'list_id' => $list2->id,
        ];

        $tasks = $this->service->getPaginatedTasks($filters);
        $this->assertCount(1, $tasks->items());

        $filters = [
            'search' => 'abc',
            'priority' => 'normal',
        ];
        $tasks = $this->service->getPaginatedTasks($filters);
        $this->assertCount(0, $tasks->items());

        $filters = [];
        $tasks = $this->service->getPaginatedTasks($filters);
        $this->assertCount(3, $tasks->items());

        $filters = [
            'list_id' => $list2->id,
        ];
        $tasks = $this->service->getPaginatedTasks($filters);
        $this->assertCount(1, $tasks->items());

        $filters = [
            'list_id' => 10,
        ];
        $tasks = $this->service->getPaginatedTasks($filters);
        $this->assertCount(0, $tasks->items());
    }

    public function test_create_task(): void
    {
        $data = $this->returnData();
        $task = $this->service->create($data);
        $this->assertInstanceOf(Task::class, $task);
        $this->assertTaskMatches($data, $task);
        $this->assertModelExists($task);
    }

    public function test_update_task(): void
    {
        $data = $this->returnData();
        $task = $this->service->create($data);

        $updateData = $this->returnData([
            'title' => 'House 123',
            'description' => 'A beautiful 123 house',
            'priority' => 'high',
            'is_complete' => true,
            'list_id' => $task->list_id,
        ]);

        $task = $this->service->update($updateData, $task);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertTaskMatches($updateData, $task);
        $this->assertModelExists($task);
    }

    public function test_delete_task(): void
    {
        $data = $this->returnData();
        $task = $this->service->create($data);
        $this->service->delete($task);
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);

        $this->assertModelMissing($task);
    }

    private function assertTaskMatches(array $data, Task $task): void
    {
        $this->assertEquals($data['title'], $task->title);
        $this->assertEquals($data['description'], $task->description);
        $this->assertEquals($data['priority'], $task->priority);
        $this->assertEquals($data['is_complete'], $task->is_complete);
        $this->assertEquals($data['list_id'], $task->list_id);
    }

    private function returnData(array $overrides = []): array
    {
        return Task::factory()->make($overrides)->toArray();
    }

    private function createTask(array $overrides = []): Task
    {
        return Task::factory()->create($overrides);
    }
}
