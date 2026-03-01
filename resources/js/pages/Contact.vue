<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardFooter from '@/components/ui/card/CardFooter.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import Input from '@/components/ui/input/Input.vue';
import Spinner from '@/components/ui/spinner/Spinner.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { contact } from '@/routes';
import { store as contactStore } from '@/routes/contact';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Contact',
        href: contact().url,
    },
];
</script>

<template>
    <Head title="Contact" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 px-2 md:min-h-min"
            >
                <Card class="mx-auto my-6 px-2">
                    <CardHeader>
                        <CardTitle class="text-center">Contact us</CardTitle>
                        <CardDescription class="text-center">
                            Get in touch with us
                        </CardDescription>
                    </CardHeader>

                    <Form
                        v-bind="contactStore.form()"
                        reset-on-success
                        :options="{ preserveScroll: true }"
                        v-slot="{ errors, processing }"
                    >
                        <CardContent class="space-y-6">
                            <div class="grid gap-2">
                                <label for="name" class="text-sm font-medium"
                                    >Name</label
                                >
                                <Input
                                    id="name"
                                    name="name"
                                    type="text"
                                    placeholder="John Doe"
                                    autofocus
                                    required
                                    :aria-invalid="
                                        errors.name ? 'true' : 'false'
                                    "
                                />
                                <InputError
                                    :message="(errors.name as string) || ''"
                                />
                            </div>
                            <div class="grid gap-2">
                                <label for="email" class="text-sm font-medium"
                                    >Email</label
                                >
                                <Input
                                    id="email"
                                    name="email"
                                    type="email"
                                    placeholder="john@doe.com"
                                    required
                                    :aria-invalid="
                                        errors.email ? 'true' : 'false'
                                    "
                                />
                                <InputError
                                    :message="(errors.email as string) || ''"
                                />
                            </div>
                            <div class="grid gap-2">
                                <label for="phone" class="text-sm font-medium"
                                    >Phone</label
                                >
                                <Input
                                    id="phone"
                                    name="phone"
                                    type="text"
                                    placeholder="Phone"
                                    :aria-invalid="
                                        errors.phone ? 'true' : 'false'
                                    "
                                />
                                <InputError
                                    :message="(errors.phone as string) || ''"
                                />
                            </div>
                            <div class="grid gap-2">
                                <label for="message" class="text-sm font-medium"
                                    >Message</label
                                >
                                <textarea
                                    id="message"
                                    name="message"
                                    rows="4"
                                    placeholder="Message"
                                    required
                                    :aria-invalid="
                                        errors.message ? 'true' : 'false'
                                    "
                                    class="flex min-h-[120px] w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none selection:bg-primary selection:text-primary-foreground placeholder:text-muted-foreground disabled:cursor-not-allowed disabled:opacity-50 dark:bg-input/30"
                                />
                                <InputError
                                    :message="(errors.message as string) || ''"
                                />
                            </div>
                        </CardContent>
                        <CardFooter class="mt-2 flex justify-end">
                            <Button
                                type="submit"
                                :processing="processing"
                                class="w-full"
                                :disabled="processing"
                                :aria-busy="processing"
                            >
                                <Spinner
                                    v-if="processing"
                                    class="mr-2 h-4 w-4 animate-spin"
                                />
                                <span> Submit </span>
                            </Button>
                        </CardFooter>
                    </Form>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
