<script setup lang="ts">
import { Head, router, Form, useForm, Link } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import {
    Loader2,
    SearchIcon,
    CheckCircle2,
    Circle,
    Trash2,
    Pencil,
    PlusIcon,
} from 'lucide-vue-next';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import Modal from '@/components/Modal.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { DialogFooter } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index as tasksIndex, update, store } from '@/routes/tasks';
import { type BreadcrumbItem } from '@/types';

interface Task {
    id: number;
    title: string;
    description?: string;
    priority: 'low' | 'normal' | 'high';
    is_complete?: boolean;
    created_at: string;
    list: List;
    list_id: number;
}

interface List {
    id: number;
    name: string;
    color?: string;
    tasks_count?: number;
    created_at: string;
    updated_at: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginationTasks {
    data: Task[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links?: PaginationLink[];
}

interface Props {
    tasks: PaginationTasks;
    lists: Array<List>;
    filters: {
        search?: string;
        priority?: string;
        list_id?: number;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Tasks',
        href: tasksIndex().url,
    },
];

const search = ref(props.filters.search || '');
const priority = ref(props.filters.priority || '');
const list_id = ref(props.filters.list_id || 0);

const isCreateDialog = ref(false);
const isEditDialog = ref(false);
const editingTask = ref<Task | null>(null);
const deletingTaskId = ref<number | null>(null);

const createForm = useForm({
    title: '',
    description: '',
    priority: 'normal',
    list_id: props.filters.list_id || null,
});

const editForm = useForm({
    title: '',
    description: '',
    priority: 'normal',
});

const openCreateDialog = () => {
    isCreateDialog.value = true;
};

const openEditDialog = (task: Task) => {
    editingTask.value = { ...task };
    editForm.title = task.title;
    editForm.description = task.description || '';
    editForm.priority = task.priority || 'normal';
    isEditDialog.value = true;
};

const getPriorityVariant = (
    priority: string,
): 'default' | 'secondary' | 'destructive' => {
    switch (priority) {
        case 'low':
            return 'secondary';
        case 'high':
            return 'destructive';
        default:
            return 'default';
    }
};

watchDebounced(
    [search, priority, list_id],
    () => {
        router.get(
            tasksIndex().url,
            {
                search: search.value || undefined,
                priority: priority.value || undefined,
                list_id: list_id.value || undefined,
            },
            {
                preserveScroll: true,
                preserveState: true,
            },
        );
    },
    { debounce: 500 },
);

const clearFilters = () => {
    search.value = '';
    priority.value = '';
    list_id.value = 0;
};

const toggleTaskCompletion = (task: Task) => {
    router.put(
        update(task.id),
        {
            title: task.title,
            description: task.description,
            priority: task.priority,
            is_complete: !task.is_complete,
        },
        {
            preserveScroll: true,
        },
    );
};

const createTask = () => {
    createForm.post(store().url, {
        preserveScroll: true,
        onSuccess: () => {
            isCreateDialog.value = false;
            createForm.reset();
        },
    });
};

const updateTask = () => {
    if (!editingTask.value) return;
    editForm.put(update(editingTask.value.id).url, {
        preserveScroll: true,
        onSuccess: () => {
            isEditDialog.value = false;
            editForm.reset();
        },
    });
};

const deleteTask = (taskId: number) => {
    if (confirm('Are you sure you want to delete this task?')) {
        deletingTaskId.value = taskId;
        router.delete(update(taskId).url, {
            preserveScroll: true,
            onSuccess: () => {
                deletingTaskId.value = null;
            },
        });
    }
};
</script>

<template>
    <Head title="Tasks" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <div class="flex flex-col justify-between">
                <div class="flex justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">All Tasks</h1>
                        <p class="text-muted-foreground">
                            View and manage all your tasks ({{
                                tasks.total
                            }}
                            total)
                        </p>
                    </div>
                    <div>
                        <Button @click="openCreateDialog" class="mt-4">
                            <PlusIcon class="h-4 w-4" />
                            Add Task
                        </Button>
                    </div>
                </div>

                <Card class="my-2">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle>Filters</CardTitle>
                            <Button
                                variant="outline"
                                size="sm"
                                @click="clearFilters"
                                >Clear</Button
                            >
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-4 md:grid-cols-3">
                            <div class="space-y-2">
                                <label for="search">Search</label>
                                <div class="relative">
                                    <SearchIcon
                                        class="absolute top-2.5 left-2 h-4 w-4 text-muted-foreground"
                                    />
                                    <Input
                                        id="search"
                                        name="search"
                                        type="text"
                                        class="pl-7"
                                        placeholder="Search"
                                        v-model="search"
                                        required
                                    />
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label for="search">List</label>
                                <select
                                    id="list_id"
                                    name="list_id"
                                    v-model="list_id"
                                    class="bg-neutral-secondary-medium border-default-medium text-heading rounded-base focus:ring-brand focus:border-brand placeholder:text-body block w-full border px-3 py-2.5 text-sm shadow-xs dark:bg-primary-foreground"
                                >
                                    <option value="">All</option>
                                    <option
                                        v-for="list in lists"
                                        :key="list.id"
                                        :value="list.id"
                                    >
                                        {{ list.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label for="search">Priority</label>
                                <select
                                    id="priority"
                                    name="priority"
                                    v-model="priority"
                                    class="bg-neutral-secondary-medium border-default-medium text-heading rounded-base focus:ring-brand focus:border-brand placeholder:text-body block w-full border px-3 py-2.5 text-sm shadow-xs dark:bg-primary-foreground"
                                >
                                    <option value="">All</option>
                                    <option value="low">Low</option>
                                    <option value="normal">Normal</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card class="my-2">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle
                                >Tasks ({{ tasks.data.length }} of
                                {{ tasks.total }})</CardTitle
                            >
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div v-if="tasks.data.length > 0" class="space-y-4">
                            <div class="rounded-md border">
                                <table class="w-full caption-bottom text-sm">
                                    <thead class="[&_tr]:border-b">
                                        <tr
                                            class="border-b transition-colors hover:bg-muted/50"
                                        >
                                            <th
                                                class="h=12 px-4 text-left align-middle font-medium text-muted-foreground"
                                            >
                                                Title
                                            </th>
                                            <th
                                                class="h=12 w-37.5 px-4 text-left align-middle font-medium text-muted-foreground"
                                            >
                                                List
                                            </th>
                                            <th
                                                class="h=12 w-25 px-4 text-left align-middle font-medium text-muted-foreground"
                                            >
                                                Priority
                                            </th>
                                            <th
                                                class="h=12 w-25 px-4 text-left align-middle font-medium text-muted-foreground"
                                            >
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="[&_tr:last-child]:border-0">
                                        <tr
                                            v-for="task in tasks.data"
                                            :key="task.id"
                                            class="border-b transition-colors hover:bg-muted/50"
                                        >
                                            <td class="p-4 align-middle">
                                                <div
                                                    class="flex items-center gap-3"
                                                >
                                                    <Button
                                                        @click="
                                                            toggleTaskCompletion(
                                                                task,
                                                            )
                                                        "
                                                        size="sm"
                                                        :variant="
                                                            getPriorityVariant(
                                                                task.priority,
                                                            )
                                                        "
                                                    >
                                                        <CheckCircle2
                                                            v-if="
                                                                task.is_complete
                                                            "
                                                            class="h-4 w-4"
                                                        />
                                                        <Circle
                                                            v-else
                                                            class="h-4 w-4"
                                                        />
                                                    </Button>
                                                    <span
                                                        :class="{
                                                            'text-muted-foreground line-through':
                                                                task.is_complete,
                                                        }"
                                                        >{{ task.title }}</span
                                                    >
                                                </div>
                                            </td>
                                            <td class="p-4 align-middle">
                                                <div
                                                    class="flex items-center gap-2"
                                                >
                                                    <div
                                                        class="h-3 w-3 rounded-full"
                                                        :style="{
                                                            backgroundColor:
                                                                task.list
                                                                    .color ||
                                                                '#6366f1',
                                                        }"
                                                    ></div>
                                                    <span
                                                        :class="{
                                                            'text-muted-foreground line-through':
                                                                task.is_complete,
                                                        }"
                                                        >{{
                                                            task.list.name
                                                        }}</span
                                                    >
                                                </div>
                                            </td>
                                            <td class="p-4 align-middle">
                                                <Badge
                                                    :variant="
                                                        getPriorityVariant(
                                                            task.priority,
                                                        )
                                                    "
                                                    >{{ task.priority }}</Badge
                                                >
                                            </td>
                                            <td class="p-4 align-middle">
                                                <div
                                                    class="flex items-center gap-2"
                                                >
                                                    <Button
                                                        size="sm"
                                                        variant="outline"
                                                        @click="
                                                            openEditDialog(task)
                                                        "
                                                    >
                                                        <Pencil
                                                            class="h-4 w-4"
                                                        />
                                                    </Button>
                                                    <Button
                                                        size="sm"
                                                        variant="destructive"
                                                        @click="
                                                            deleteTask(task.id)
                                                        "
                                                    >
                                                        <Loader2
                                                            v-if="
                                                                deletingTaskId ===
                                                                task.id
                                                            "
                                                            class="mr-2 h-4 w-4 animate-spin"
                                                        />
                                                        <Trash2
                                                            v-else
                                                            class="h-4 w-4"
                                                        />
                                                    </Button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="flex items-center justify-center">
                                <p class="text-sm text-muted-foreground">
                                    Showing {{ tasks.data.length }} of
                                    {{ tasks.total }} tasks
                                </p>
                                <div class="flex items-center gap-2">
                                    <Link
                                        v-for="link in tasks.links"
                                        :key="link.label"
                                        :href="link.url || '#'"
                                        class="rounded-md px-3 py-1 text-sm text-muted-foreground"
                                        :class="[
                                            link.active
                                                ? 'bg-primary text-foreground'
                                                : link.url
                                                  ? 'hover:bg-muted/60'
                                                  : 'cursor-not-allowed opacity-50',
                                        ]"
                                        :preserve-scroll="true"
                                        :preserve-state="true"
                                    >
                                        <span v-html="link.label" />
                                    </Link>
                                </div>
                            </div>
                        </div>
                        <div
                            v-else
                            class="flex flex-col items-center justify-center py-12"
                        >
                            <p class="mb-4 text-muted-foreground">
                                No tasks found.
                            </p>
                            <p class="text-sm text-muted-foreground">
                                Create a new task to get started.
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <Modal
                    title="Add new Task"
                    description="Create a new task and assign it to a list"
                    v-model="isCreateDialog"
                >
                    <Form @submit.prevent="createTask" class="spaces-y-4">
                        <div class="space-y-2">
                            <label for="title">Task title</label>
                            <Input
                                id="title"
                                name="title"
                                type="text"
                                placeholder="title"
                                v-model="createForm.title"
                                required
                                :aria-invalid="
                                    createForm.errors?.title ? 'true' : 'false'
                                "
                            />
                            <InputError :message="createForm.errors?.title" />
                        </div>
                        <div class="space-y-2">
                            <label for="description">List</label>
                            <select
                                id="list_id"
                                name="list_id"
                                required
                                v-model="createForm.list_id"
                                class="bg-neutral-secondary-medium border-default-medium text-heading rounded-base focus:ring-brand focus:border-brand placeholder:text-body block w-full border px-3 py-2.5 text-sm shadow-xs dark:bg-primary-foreground"
                            >
                                <option
                                    v-for="list in lists"
                                    :key="'createForm-' + list.id"
                                    :value="list.id"
                                >
                                    {{ list.name }}
                                </option>
                            </select>
                            <InputError :message="createForm.errors?.list_id" />
                        </div>
                        <div class="space-y-2">
                            <label for="description">Description</label>
                            <textarea
                                id="description"
                                name="description"
                                type="text"
                                class="bg-neutral-secondary-medium border-default-medium text-heading rounded-base focus:ring-brand focus:border-brand placeholder:text-body block w-full border px-3 py-2.5 text-sm shadow-xs dark:bg-primary-foreground"
                                placeholder="description"
                                v-model="createForm.description"
                                required
                                :aria-invalid="
                                    createForm.errors?.description
                                        ? 'true'
                                        : 'false'
                                "
                            />
                            <InputError
                                :message="createForm.errors?.description"
                            />
                        </div>
                        <div class="space-y-2">
                            <label for="description">Priority</label>
                            <select
                                id="priority"
                                name="priority"
                                v-model="createForm.priority"
                                class="bg-neutral-secondary-medium border-default-medium text-heading rounded-base focus:ring-brand focus:border-brand placeholder:text-body block w-full border px-3 py-2.5 text-sm shadow-xs dark:bg-primary-foreground"
                            >
                                <option value="low">Low</option>
                                <option value="normal">Normal</option>
                                <option value="high">High</option>
                            </select>
                            <InputError
                                :message="createForm.errors?.priority"
                            />
                        </div>

                        <DialogFooter>
                            <Button
                                type="button"
                                variant="outline"
                                @click="isCreateDialog = false"
                                :disabled="createForm.processing"
                                >Cancel</Button
                            >
                            <Button
                                type="submit"
                                :processing="createForm.processing"
                                :disabled="createForm.processing"
                            >
                                <Loader2
                                    v-if="createForm.processing"
                                    class="mr-2 h-4 w-4 animate-spin"
                                />

                                {{
                                    createForm.processing ? 'Saving...' : 'Save'
                                }}
                            </Button>
                        </DialogFooter>
                    </Form>
                </Modal>

                <Modal
                    title="Edit Task"
                    description="Update the task of the task"
                    v-model="isEditDialog"
                >
                    <Form @submit.prevent="updateTask" class="spaces-y-4">
                        <div class="space-y-2">
                            <label for="title">Task title</label>
                            <Input
                                id="title"
                                name="title"
                                type="text"
                                placeholder="title"
                                v-model="editForm.title"
                                required
                                :aria-invalid="
                                    editForm.errors?.title ? 'true' : 'false'
                                "
                            />
                            <InputError :message="editForm.errors?.title" />
                        </div>
                        <div class="space-y-2">
                            <label for="description">Description</label>
                            <textarea
                                id="description"
                                name="description"
                                type="text"
                                class="bg-neutral-secondary-medium border-default-medium text-heading rounded-base focus:ring-brand focus:border-brand placeholder:text-body block w-full border px-3 py-2.5 text-sm shadow-xs dark:bg-primary-foreground"
                                placeholder="description"
                                v-model="editForm.description"
                                required
                                :aria-invalid="
                                    editForm.errors?.description
                                        ? 'true'
                                        : 'false'
                                "
                            />
                            <InputError
                                :message="editForm.errors?.description"
                            />
                        </div>
                        <div class="space-y-2">
                            <label for="description">Priority</label>
                            <select
                                id="priority"
                                name="priority"
                                v-model="editForm.priority"
                                class="bg-neutral-secondary-medium border-default-medium text-heading rounded-base focus:ring-brand focus:border-brand placeholder:text-body block w-full border px-3 py-2.5 text-sm shadow-xs dark:bg-primary-foreground"
                            >
                                <option value="low">Low</option>
                                <option value="normal">Normal</option>
                                <option value="high">High</option>
                            </select>
                            <InputError :message="editForm.errors?.priority" />
                        </div>

                        <DialogFooter>
                            <Button
                                type="button"
                                variant="outline"
                                @click="isCreateDialog = false"
                                :disabled="editForm.processing"
                                >Cancel</Button
                            >
                            <Button
                                type="submit"
                                :processing="editForm.processing"
                                :disabled="editForm.processing"
                            >
                                <Loader2
                                    v-if="editForm.processing"
                                    class="mr-2 h-4 w-4 animate-spin"
                                />

                                {{ editForm.processing ? 'Saving...' : 'Save' }}
                            </Button>
                        </DialogFooter>
                    </Form>
                </Modal>
            </div>
        </div>
    </AppLayout>
</template>
