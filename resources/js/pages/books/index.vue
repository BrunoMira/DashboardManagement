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
import { index, index as book, destroy, update, store } from '@/routes/bookes';
import { local } from '@/routes/storage';
import { type BreadcrumbItem } from '@/types';


interface Category {
    id: number;
    name: string;
}

interface Book {
    id: number;
    title: string;
    author: string;
    price: number;
    cover_image: string;
    full_image_path: string;
    category_id?: number;
    category?: Category;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Props {
    books: { data: Book[]; links?: PaginationLink[] };
    categories: Category[];
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Bookes',
        href: book().url,
    },
];

const props = defineProps<Props>();

const items = computed(() => props.books.data ?? []);
const createOpen = ref(false);
const editOpen = ref(false);
const deleteOpen = ref(false);

const selected = ref<Book | null>(null);
const newBook = ref('');

const openEdit = (book: Book) => {
    selected.value = book;
    editOpen.value = true;
};

const openCreate = () => {
    selected.value = null;
    newBook.value = '';
    createOpen.value = true;
};

const openDelete = (book: Book) => {
    selected.value = book;
    deleteOpen.value = true;
};

const imageUrl = (path?: string) => {
    if (!path) return '';
    return local.url(path);
};

</script>

<template>
    <Head title="Bookes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card class="m-4 overflow-hidden">
            <CardHeader>
                <div class="flex items-center justify-between">
                    <CardTitle class="text-center">Bookes</CardTitle>
                    <Button size="sm" @click="openCreate">Add Book</Button>
                </div>
            </CardHeader>
            <CardContent>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b">
                                <th class="px-3 py-2 text-sm text-muted-foreground">Image</th>
                                <th class="px-3 py-2 text-sm text-muted-foreground">Title</th>
                                <th class="px-3 py-2 text-sm text-muted-foreground">Author</th>
                                <th class="px-3 py-2 text-sm text-muted-foreground">Price</th>
                                <th class="px-3 py-2 text-sm text-muted-foreground">Category</th>
                                <th class="px-3 py-2 text-sm text-muted-foreground">Actions</th>
                            </tr>
                        </thead>
                        <tbody v-if="items.length">
                            <tr v-for="book in items" :key="book.id" class="border-b hover:bg-muted/30">
                                <td class="px-3 py-2">
                                    <img v-if="book.cover_image" :src="book.full_image_path" class="h-14 w-20 rounded border bg-muted" />
                                    <div v-else class="h-14 w-20 rounded border bg-muted"></div>
                                </td>
                                <td class="px-3 py-2">
                                    {{ book.title }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ book.author }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ book.price }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ book.category?.name }}
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex gap-2">
                                        <Button size="sm" variant="outline" @click="openEdit(book)">Edit</Button>
                                        <Button size="sm" variant="destructive" @click="openDelete(book)">Delete</Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr class="border-b">
                                <td class="px-3 py-2 text-center" colspan="6">No books found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="props.books?.links?.length" class="mt-4 flex items-center gap-2">
                    <Link v-for="link in props.books.links" :key="link.label" :href="link.url || index().url"
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

        <Modal title="Edit Book" description="Update the book of the book" v-model="editOpen">
            <Form
                v-if="selected"
                v-bind="update.form(selected.id)"
                enctype="multipart/form-data"
                reset-on-error
                @success="editOpen = false"
                v-slot="{errors, processing}"
                class="spaces-y-6">

                <div class="grid gap-2 mb-2">
                    <label for="title">Title</label>
                    <Input
                        id="title"
                        name="title"
                        type="text"
                        placeholder="Title"
                        :default-value="selected.title"
                        required
                        :aria-invalid="errors.title ? 'true' : 'false'"
                    />
                    <InputError :message="errors?.title" />
                </div>
                <div class="grid gap-2">
                    <label for="author">Author</label>
                    <Input
                        id="author"
                        name="author"
                        type="text"
                        placeholder="author"
                        :default-value="selected.author"
                        required
                        :aria-invalid="errors.author ? 'true' : 'false'"
                    />
                    <InputError :message="errors?.author" />
                </div>
                <div class="grid gap-2">
                    <label for="price">Price</label>
                    <Input
                        id="price"
                        name="price"
                        type="text"
                        placeholder="Price"
                        :default-value="selected.price"
                        required
                        :aria-invalid="errors.price ? 'true' : 'false'"
                    />
                    <InputError :message="errors?.price" />
                </div>
                <div class="grid gap-2">
                    <label for="category_id">Category</label>
                    <select class="border rouded p-2 w-full" v-model="selected.category_id" name="category_id" placeholder="Category" required>
                        <option selected value=""></option>
                        <option v-for="category in props.categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                    </select>
                    <InputError :message="errors?.category_id" />
                </div>
                <div class="grid gap-2">
                    <label for="cover_image">Image</label>
                    <Input
                        id="cover_image"
                        name="cover_image"
                        type="file"
                        placeholder="Image"
                        :aria-invalid="errors.cover_image ? 'true' : 'false'"
                    />
                    <div v-if="selected?.cover_image" class="text-xs text-muted-foreground">Current image shown in table</div>
                    <InputError :message="errors?.image" />
                </div>

                <DialogFooter>
                    <Button type="button" variant="outline" @click="editOpen = false" :disabled="processing">Cancel</Button>
                    <Button type="submit" :processing="processing" :disabled="processing">Save</Button>
                </DialogFooter>
            </Form>
        </Modal>


        <Modal title="Add Book" description="Add a new book to the book" v-model="createOpen">
            <Form
                v-bind="store.form()"
                enctype="multipart/form-data"
                reset-on-error@success="() => createOpen = false; newBook=''"
                @success="createOpen = false"
                v-slot="{errors, processing}"
                class="spaces-y-6">

                <div class="grid gap-2 mb-2">
                    <label for="title">Title</label>
                    <Input
                        id="title"
                        name="title"
                        type="text"
                        placeholder="Title"
                        required
                        :aria-invalid="errors.title ? 'true' : 'false'"
                    />
                    <InputError :message="errors?.title" />
                </div>
                <div class="grid gap-2">
                    <label for="author">Author</label>
                    <Input
                        id="author"
                        name="author"
                        type="text"
                        placeholder="author"
                        required
                        :aria-invalid="errors.author ? 'true' : 'false'"
                    />
                    <InputError :message="errors?.author" />
                </div>
                <div class="grid gap-2">
                    <label for="price">Price</label>
                    <Input
                        id="price"
                        name="price"
                        type="text"
                        placeholder="Price"
                        required
                        :aria-invalid="errors.price ? 'true' : 'false'"
                    />
                    <InputError :message="errors?.price" />
                </div>
                <div class="grid gap-2">
                    <label for="category_id">Category</label>
                    <select class="border rouded p-2 w-full" name="category_id" placeholder="Category" required>
                        <option selected value=""></option>
                        <option v-for="category in props.categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                    </select>
                    <InputError :message="errors?.category_id" />
                </div>
                <div class="grid gap-2">
                    <label for="cover_image">Image</label>
                    <Input
                        id="cover_image"
                        name="cover_image"
                        type="file"
                        placeholder="Image"
                        :aria-invalid="errors.cover_image ? 'true' : 'false'"
                    />
                    <InputError :message="errors?.cover_image" />
                </div>

                <DialogFooter>
                    <Button type="button" variant="outline" @click="createOpen = false" :disabled="processing">Cancel</Button>
                    <Button type="submit" :processing="processing" :disabled="processing">Save</Button>
                </DialogFooter>
            </Form>
        </Modal>

        <Modal title="Delete Book" :description="`Are you sure you want to delete ${selected?.title}  ?`" v-model="deleteOpen">
            <Form
                v-if="selected"
                v-bind="destroy.form(selected.id)"
                reset-on-error@sucess="() => deleteOpen = false; selected = null"
                v-slot="{processing}">


                    <DialogFooter>
                        <Button type="button" variant="outline" @click="deleteOpen = false" :disabled="processing">Cancel</Button>
                        <Button type="submit" :processing="processing" :disabled="processing">Delete</Button>
                    </DialogFooter>
            </Form>
        </Modal>
    </AppLayout>
</template>
