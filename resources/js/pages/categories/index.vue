<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import Modal from '@/components/Modal.vue';
import {Button} from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle} from '@/components/ui/card';
import {DialogFooter} from '@/components/ui/dialog';
import {Input} from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { index, index as category, destroy, update, store } from '@/routes/categories';
import { local } from '@/routes/storage';
import { type BreadcrumbItem } from '@/types';


interface Category {
    id: number;
    name: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Props {
    categories: { data: Category[]; links?: PaginationLink[] };
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'categories',
        href: category().url,
    },
];

const props = defineProps<Props>();

const items = computed(() => props.categories.data ?? []);
const createOpen = ref(false);
const editOpen = ref(false);
const deleteOpen = ref(false);

const selected = ref<Category | null>(null);
const newCategory = ref('');

const openEdit = (category: Category) => {
    selected.value = category;
    editOpen.value = true;
};

const openCreate = () => {
    selected.value = null;
    newCategory.value = '';
    createOpen.value = true;
};

const openDelete = (category: Category) => {
    selected.value = category;
    deleteOpen.value = true;
};

</script>

<template>
    <Head title="categories" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card class="m-4 overflow-hidden">
            <CardHeader>
                <div class="flex items-center justify-between">
                    <CardTitle class="text-center">Categories</CardTitle>
                    <Button size="sm" @click="openCreate">Add Category</Button>
                </div>
            </CardHeader>
            <CardContent>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b">
                                <th class="px-3 py-2 text-sm text-muted-foreground">Id</th>
                                <th class="px-3 py-2 text-sm text-muted-foreground">Name</th>
                                <th class="px-3 py-2 text-sm text-muted-foreground">Actions</th>
                            </tr>
                        </thead>
                        <tbody v-if="items.length">
                            <tr v-for="category in items" :key="category.id" class="border-b hover:bg-muted/30">
                                <td class="px-3 py-2">
                                    {{ category.id }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ category.name }}
                                </td>

                                <td class="px-3 py-2 flex justify-self-end">
                                    <div class="flex gap-2">
                                        <Button size="sm" variant="outline" @click="openEdit(category)">Edit</Button>
                                        <Button size="sm" variant="destructive" @click="openDelete(category)">Delete</Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr class="border-b">
                                <td class="px-3 py-2 text-center" colspan="3">No categories found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="props.categories?.links?.length" class="mt-4 flex items-center gap-2">
                    <Link v-for="link in props.categories.links" :key="link.label" :href="link.url || index().url"
                        preserve-scroll
                        class="rounded px-3 py-1 text-sm"
                        :class="[
                            link.active ? 'bg-muted text-foreground' : 'text-muted-foreground hover:bg-muted/60',
                            !link.url ? 'pointer-events-none opacity-50' : '',
                        ]"
                    >
                        <span v-html="link.label" />
                    </Link>
                </div>
            </CardContent>
        </Card>

        <Modal title="Edit Category" description="Update the name of a category" v-model="editOpen">
            <Form
                v-if="selected"
                v-bind="update.form(selected.id)"
                reset-on-error
                @success="editOpen = false"
                v-slot="{errors, processing}"
                class="spaces-y-6">

                <div class="grid gap-2  mb-2">
                    <label for="name">Name</label>
                    <Input
                        id="name"
                        name="name"
                        type="text"
                        placeholder="Name"
                        :default-value="selected.name"
                        required
                        :aria-invalid="errors.name ? 'true' : 'false'"
                    />
                    <InputError :message="errors?.name" />
                </div>
                <DialogFooter>
                    <Button type="button" variant="outline" @click="editOpen = false" :disabled="processing">Cancel</Button>
                    <Button type="submit" :processing="processing" :disabled="processing">Save</Button>
                </DialogFooter>
            </Form>
        </Modal>


        <Modal title="Add Category" description="Add a new category" v-model="createOpen">
            <Form
                v-bind="store.form()"
                reset-on-error@success="() => createOpen = false; newCategory=''"
                @success="createOpen = false"
                v-slot="{errors, processing}"
                class="spaces-y-6">

                <div class="grid gap-2 mb-2">
                    <label for="name">Name</label>
                    <Input
                        id="name"
                        name="name"
                        type="text"
                        placeholder="Name"
                        required
                        :aria-invalid="errors.name ? 'true' : 'false'"
                    />
                    <InputError :message="errors?.name" />
                </div>

                <DialogFooter >
                    <Button type="button" variant="outline" @click="createOpen = false" :disabled="processing">Cancel</Button>
                    <Button type="submit" :processing="processing" :disabled="processing">Save</Button>
                </DialogFooter>
            </Form>
        </Modal>

        <Modal title="Delete Category" :description="`Are you sure you want to delete ${selected?.name}  ?`" v-model="deleteOpen">
            <Form
                v-if="selected"
                v-bind="destroy.form(selected.id)"
                reset-on-error@sucess="() => deleteOpen = false; selected = null"
                @sucess="deleteOpen = false"
                v-slot="{processing}">


                    <DialogFooter>
                        <Button type="button" variant="outline" @click="deleteOpen = false" :disabled="processing">Cancel</Button>
                        <Button type="submit" :processing="processing" :disabled="processing">Delete</Button>
                    </DialogFooter>
            </Form>
        </Modal>
    </AppLayout>
</template>
