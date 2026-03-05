<script setup lang="ts">
    import { Form, Head, Link, router, useForm } from '@inertiajs/vue3';
    import { ExternalLink, Loader2, Trash2, Pencil, PlusIcon } from 'lucide-vue-next';
    import { ref } from 'vue';
    import InputError from '@/components/InputError.vue';
    import Modal from '@/components/Modal.vue';
    import Button from '@/components/ui/button/Button.vue';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import { DialogFooter } from '@/components/ui/dialog';
    import { Input } from '@/components/ui/input';
    import AppLayout from '@/layouts/AppLayout.vue';
    import { dashboard } from '@/routes';
    import {store, update, destroy, index as listsIndex} from '@/routes/lists';
    import tasks from '@/routes/tasks';
    import { type BreadcrumbItem } from '@/types';


    interface Task {
        id: number;
        title: string;
        description?: string;
        priority?: string;
        is_complete?: boolean;
    }

    interface List {
        id: number;
        name: string;
        color?: string;
        tasks_count?: number;
        created_at: string;
        updated_at: string;
    }

    interface Props {
        lists: Array<List>;
    }

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: dashboard().url,
        },
        {
            title: 'Lists',
            href: listsIndex().url,
        },
    ];

    const props = defineProps<Props>();

    const isCreateDialog = ref(false);
    const isEditDialog = ref(false);
    const editingList = ref<List | null>(null);
    const deletingListId = ref<number | null>(null);

    const createForm = useForm({
        name: '',
        color: '#6366f1',
    });

    const editForm = useForm({
        name: '',
        color: '#6366f1',
    });

    const openCreateDialog = () => {
        isCreateDialog.value = true;
    };

    const openEditDialog = (list: List) => {
        editingList.value = list;
        editForm.name = list.name;
        editForm.color = list.color || '#6366f1';
        isEditDialog.value = true;
    };

    const createList = () => {
        createForm.post(store.url(), {
            preserveScroll: true,
            onSuccess: () => {
                isCreateDialog.value = false;
                createForm.reset();
            },
        });
    };

    const updateList = () => {
        if(!editingList.value) return;
        editForm.put(update.url(editingList.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                isEditDialog.value = false;
                editForm.reset();
            },
        });
    }

    const deleteList = (listId: number) => {
        if (confirm('Are you sure you want to delete this list?')) {
            deletingListId.value = listId;
            router.delete(destroy.url(listId), {
                preserveScroll: true,
                onSuccess: () => {
                    deletingListId.value = null;
                },
            });
        }
    };
</script>

<template>
    <Head title="Lists" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Lists</h1>
                    <p class="text-muted-foreground">
                        Manage your lists here.
                    </p>
                </div>

                <Button @click="openCreateDialog" class="mt-4">
                    <PlusIcon class="h-4 w-4" />
                    Add List
                </Button>
            </div>

                <div v-if="props.lists.length" class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    <Card v-for="list in props.lists" :key="list.id" class="hover:shadow-md transition-shadow group relative">
                        <CardHeader>
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full" :style="{backgroundColor: list.color || '#6366f1'}"></div>
                                    <CardTitle class="text-lg">{{ list.name }}</CardTitle>
                                </div>

                                <span class="text-2xl font-bold text-muted-foreground">{{ list.tasks_count || 0 }}</span>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm text-muted-foreground mb-4">
                                {{ list.tasks_count || 0 }} {{ list.tasks_count === 1 ? 'Task' : 'Tasks' }}
                            </p>
                            <div class="flex gap-2">
                                <Link :href="tasks.index({query: {list_id: list.id}})" class="flex-1">
                                    <Button variant="outline" size="sm" class="w-full"><ExternalLink class="h-4 w-4 mr-2" /> View</Button>
                                </Link>
                                <Button variant="outline" size="sm" @click="openEditDialog(list)">
                                    <Pencil class="h-4 w-4" />
                                </Button>
                                <Button variant="destructive"
                                        size="sm"
                                        @click="deleteList(list.id)"
                                        :disabled="deletingListId === list.id"
                                >
                                    <Loader2 v-if="deletingListId === list.id" class="h-4 w-4 animate-spin mr-2" />
                                    <Trash2 v-else class="h-4 w-4" />
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>
                <Card v-else>
                    <CardContent class="flex flex-col items-center justify-center py-12">
                        <p class="text-muted-foreground mb-4">No lists found.</p>
                        <p class="text-sm text-muted-foreground">Create a new list to get started.</p>
                    </CardContent>
                </Card>

                <Modal
                    title="Add List"
                    description="Add a new list to the list"
                    v-model="isCreateDialog"
                >
                    <Form
                        @submit.prevent="createList"
                        class="spaces-y-4"
                    >
                        <div class="space-y-2">
                            <label for="name">Name</label>
                            <Input
                                id="name"
                                name="name"
                                type="text"
                                placeholder="Name"
                                v-model="createForm.name"
                                required
                                :aria-invalid="createForm.errors?.name ? 'true' : 'false'"
                            />
                            <InputError :message="createForm.errors?.name" />
                        </div>
                        <div class="space-y-2">
                            <label for="color">Color</label>
                            <Input
                                id="color"
                                name="color"
                                type="color"
                                placeholder="Color"
                                v-model="createForm.color"
                                :aria-invalid="createForm.errors?.color ? 'true' : 'false'"
                            />
                            <InputError :message="createForm.errors?.color" />
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
                                <Loader2 v-if="createForm.processing" class="h-4 w-4 animate-spin mr-2" />

                                {{ createForm.processing ? 'Saving...' : "Save" }}
                            </Button>
                        </DialogFooter>
                    </Form>
                </Modal>

                <Modal
                    title="Edit List"
                    description="Update the list of the list"
                    v-model="isEditDialog"
                >
                    <Form
                        @submit.prevent="updateList"
                        class="spaces-y-4"
                    >
                        <div class="space-y-2">
                            <label for="name">Name</label>
                            <Input
                                id="name"
                                name="name"
                                type="text"
                                v-model="editForm.name"
                                placeholder="Name"
                                required
                                :aria-invalid="editForm.errors?.name ? 'true' : 'false'"
                            />
                            <InputError :message="editForm.errors?.name" />
                        </div>
                        <div class="space-y-2">
                            <label for="color">Color</label>
                            <Input
                                id="color"
                                name="color"
                                type="color"
                                v-model="editForm.color"
                                placeholder="Color"
                                :aria-invalid="editForm.errors?.color ? 'true' : 'false'"
                            />
                            <InputError :message="editForm.errors?.color" />
                        </div>

                        <DialogFooter>
                            <Button
                                type="button"
                                variant="outline"
                                @click="isEditDialog = false"
                                :disabled="editForm.processing"
                                >Cancel</Button
                            >
                            <Button
                                type="submit"
                                :processing="editForm.processing"
                                :disabled="editForm.processing"
                            >
                                <Loader2 v-if="editForm.processing" class="h-4 w-4 animate-spin mr-2" />

                                {{ editForm.processing ? 'Saving...' : "Save" }}
                            </Button>
                        </DialogFooter>
                    </Form>
                </Modal>
        </div>
    </AppLayout>
</template>
