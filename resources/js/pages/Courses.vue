<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardFooter from '@/components/ui/card/CardFooter.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { courses as coursesIndex } from '@/routes';
import { type BreadcrumbItem } from '@/types';

interface Course {
    id: string;
    name: string;
    description: string;
    image: string;
    price: string;
    created_at: string;
    updated_at: string;
}

interface Props {
    courses: Course[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Courses',
        href: coursesIndex().url,
    },
];
</script>

<template>
    <Head title="Courses" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-3 overflow-x-auto rounded-xl p-4"
        >
            <div class="mb-3 text-center">
                <h1
                    class="mb-4 text-4xl font-bold text-gray-900 dark:text-white"
                >
                    Our Courses
                </h1>
                <p
                    class="mx-auto max-w-3xl text-center text-sm text-gray-500 dark:text-gray-400"
                >
                    We offer a variety of courses to suit your needs.
                </p>
            </div>
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <Card
                    v-for="course in props.courses"
                    :key="course.id"
                    class="transition-border shadow-md transition-shadow duration-1000 hover:border-2 hover:shadow-lg"
                >
                    <div
                        class="relative h-48 w-full overflow-hidden rounded-t-xl"
                    >
                        <img
                            :src="course.image"
                            :alt="course.name"
                            class="h-full w-full object-cover"
                        />
                        <div
                            class="absolute top-4 right-4 rounded-full bg-blue-600 px-3 py-1 text-sm font-semibold text-white"
                        >
                            ${{ course.price }}
                        </div>
                    </div>
                    <CardHeader>
                        <CardTitle
                            class="text-lg text-gray-900 dark:text-white"
                            >{{ course.name }}</CardTitle
                        >
                        <CardDescription
                            class="line-clamp-2 text-gray-600 dark:text-gray-300"
                            >{{ course.description }}</CardDescription
                        >
                    </CardHeader>
                    <CardContent class="p-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Created At: {{ course.created_at }}
                        </p>
                    </CardContent>
                    <CardFooter>
                        <button>Enroll now</button>
                    </CardFooter>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
