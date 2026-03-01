<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import Modal from '@/components/Modal.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { DialogFooter } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    index,
    index as property,
    destroy,
    update,
    store,
} from '@/routes/properties';
import { local } from '@/routes/storage';
import { type BreadcrumbItem } from '@/types';

interface Property {
    id: number;
    title: string;
    location: string;
    price: number;
    description?: string;
    image?: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Props {
    properties: { data: Property[]; links?: PaginationLink[] };
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Properties',
        href: property().url,
    },
];

const props = defineProps<Props>();

const items = computed(() => props.properties.data ?? []);
const createOpen = ref(false);
const editOpen = ref(false);
const deleteOpen = ref(false);

const selected = ref<Property | null>(null);
const newLocation = ref('');

const openEdit = (property: Property) => {
    selected.value = property;
    editOpen.value = true;
};

const openCreate = () => {
    selected.value = null;
    newLocation.value = '';
    createOpen.value = true;
};

const openDelete = (property: Property) => {
    selected.value = property;
    deleteOpen.value = true;
};

const imageUrl = (path?: string) => {
    if (!path) return '';
    return local.url(path);
};

const decdeEntites = (str: string) => {
    const textArea = document.createElement('textarea');
    textArea.innerHTML = str;
    return textArea.value;
};

const formatEmbed = (input?: string) => {
    if (!input) return '';
    const decoded = decdeEntites(input);
    return decoded
        .replace(/height="[0-9]*"/g, 'height="100%"')
        .replace(/width="[0-9]*"/g, 'width="100%"');
};
</script>

<template>
    <Head title="Properties" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Card class="m-4 overflow-hidden">
            <CardHeader>
                <div class="flex items-center justify-between">
                    <CardTitle class="text-center">Properties</CardTitle>
                    <Button size="sm" @click="openCreate">Add Location</Button>
                </div>
            </CardHeader>
            <CardContent>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b">
                                <th
                                    class="px-3 py-2 text-sm text-muted-foreground"
                                >
                                    Image
                                </th>
                                <th
                                    class="px-3 py-2 text-sm text-muted-foreground"
                                >
                                    Title
                                </th>
                                <th
                                    class="px-3 py-2 text-sm text-muted-foreground"
                                >
                                    Location
                                </th>
                                <th
                                    class="px-3 py-2 text-sm text-muted-foreground"
                                >
                                    Price
                                </th>
                                <th
                                    class="px-3 py-2 text-sm text-muted-foreground"
                                >
                                    Description
                                </th>
                                <th
                                    class="px-3 py-2 text-sm text-muted-foreground"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="property in items"
                                :key="property.id"
                                class="border-b hover:bg-muted/30"
                            >
                                <td class="px-3 py-2">
                                    <img
                                        v-if="property.image"
                                        :src="imageUrl(property.image)"
                                        class="h-14 w-20 rounded border bg-muted"
                                    />
                                    <div
                                        v-else
                                        class="h-14 w-20 rounded border bg-muted"
                                    ></div>
                                </td>
                                <td class="px-3 py-2">
                                    {{ property.title }}
                                </td>
                                <td class="px-3 py-2">
                                    <div
                                        v-if="formatEmbed(property.location)"
                                        style="
                                            width: 140px;
                                            height: 70px;
                                            overflow: hidden;
                                            border-radius: 8px;
                                        "
                                    >
                                        {{ property.location }}
                                    </div>
                                </td>
                                <td class="px-3 py-2">
                                    {{ property.price }}
                                </td>
                                <td class="px-3 py-2">
                                    <div
                                        html
                                        class="text-sm text-muted-foreground"
                                    >
                                        {{
                                            property.description ||
                                            'No description'
                                        }}
                                    </div>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex gap-2">
                                        <Button
                                            size="sm"
                                            variant="outline"
                                            @click="openEdit(property)"
                                            >Edit</Button
                                        >
                                        <Button
                                            size="sm"
                                            variant="destructive"
                                            @click="openDelete(property)"
                                            >Delete</Button
                                        >
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-if="props.properties?.links?.length"
                    class="mt-4 flex items-center gap-2"
                >
                    <Link
                        v-for="link in props.properties.links"
                        :key="link.label"
                        :href="link.url || index().url"
                        preserve-scroll
                        class="rounded px-3 py-1 text-sm"
                        :class="[
                            link.active
                                ? 'bg-muted text-foreground'
                                : 'text-muted-foreground hover:bg-muted/60',
                            !link.url ? 'pointer-events-none opacity-50' : '',
                        ]"
                    >
                        <span v-html="link.label" />
                    </Link>
                </div>
            </CardContent>
        </Card>

        <Modal
            title="Edit Location"
            description="Update the location of the property"
            v-model="editOpen"
        >
            <Form
                v-if="selected"
                v-bind="update.form(selected.id)"
                enctype="multipart/form-data"
                reset-on-error
                @success="editOpen = false"
                v-slot="{ errors, processing }"
                class="spaces-y-6"
            >
                <div class="grid gap-2">
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
                    <label for="location">Location</label>
                    <Input
                        id="location"
                        name="location"
                        type="text"
                        placeholder="Location"
                        :default-value="selected.location"
                        required
                        :aria-invalid="errors.location ? 'true' : 'false'"
                    />
                    <InputError :message="errors?.location" />
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
                    <label for="description">Description</label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        placeholder="Description"
                        :default-value="selected.description"
                        :aria-invalid="errors.description ? 'true' : 'false'"
                        class="flex min-h-[120px] w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none selection:bg-primary selection:text-primary-foreground placeholder:text-muted-foreground disabled:cursor-not-allowed disabled:opacity-50 dark:bg-input/30"
                    />
                    <InputError :message="errors?.description" />
                </div>
                <div class="grid gap-2">
                    <label for="image">Image</label>
                    <Input
                        id="image"
                        name="image"
                        type="file"
                        placeholder="Image"
                        :aria-invalid="errors.image ? 'true' : 'false'"
                    />
                    <div
                        v-if="selected?.image"
                        class="text-xs text-muted-foreground"
                    >
                        Current image shown in table
                    </div>
                    <InputError :message="errors?.image" />
                </div>

                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        @click="editOpen = false"
                        :disabled="processing"
                        >Cancel</Button
                    >
                    <Button
                        type="submit"
                        :processing="processing"
                        :disabled="processing"
                        >Save</Button
                    >
                </DialogFooter>
            </Form>
        </Modal>

        <Modal
            title="Add Location"
            description="Add a new location to the property"
            v-model="createOpen"
        >
            <Form
                v-bind="store.form()"
                enctype="multipart/form-data"
                reset-on-error@success="() => createOpen = false; newLocation=''"
                @success="createOpen = false"
                v-slot="{ errors, processing }"
                class="spaces-y-6"
            >
                <div class="grid gap-2">
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
                    <label for="location">Location</label>
                    <Input
                        id="location"
                        name="location"
                        type="text"
                        placeholder="Location"
                        required
                        :aria-invalid="errors.location ? 'true' : 'false'"
                    />
                    <InputError :message="errors?.location" />
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
                    <label for="description">Description</label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        placeholder="Description"
                        :aria-invalid="errors.description ? 'true' : 'false'"
                        class="flex min-h-[120px] w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none selection:bg-primary selection:text-primary-foreground placeholder:text-muted-foreground disabled:cursor-not-allowed disabled:opacity-50 dark:bg-input/30"
                    />
                    <InputError :message="errors?.description" />
                </div>
                <div class="grid gap-2">
                    <label for="image">Image</label>
                    <Input
                        id="image"
                        name="image"
                        type="file"
                        placeholder="Image"
                        :aria-invalid="errors.image ? 'true' : 'false'"
                    />
                    <InputError :message="errors?.image" />
                </div>

                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        @click="createOpen = false"
                        :disabled="processing"
                        >Cancel</Button
                    >
                    <Button
                        type="submit"
                        :processing="processing"
                        :disabled="processing"
                        >Save</Button
                    >
                </DialogFooter>
            </Form>
        </Modal>

        <Modal
            title="Delete Location"
            :description="`Are you sure you want to delete ${selected?.title}  ?`"
            v-model="deleteOpen"
        >
            <Form
                v-if="selected"
                v-bind="destroy.form(selected.id)"
                reset-on-error@sucess="() => deleteOpen = false; selected = null"
                v-slot="{ processing }"
            >
                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        @click="deleteOpen = false"
                        :disabled="processing"
                        >Cancel</Button
                    >
                    <Button
                        type="submit"
                        :processing="processing"
                        :disabled="processing"
                        >Delete</Button
                    >
                </DialogFooter>
            </Form>
        </Modal>
    </AppLayout>
</template>
