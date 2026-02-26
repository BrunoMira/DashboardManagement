<script setup lang="ts">
import {Dialog, DialogTitle, DialogDescription, DialogHeader, DialogContent} from '@/components/ui/dialog';


type Props = {
    title: string;
    description: string;
}

const {title, description} = defineProps<Props>();

const open = defineModel<boolean>();

</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="sm:max-w-[540px]">
            <DialogHeader>
                <DialogTitle>{{ title }}</DialogTitle>
                <DialogDescription>{{ description }}</DialogDescription>
            </DialogHeader>
            <slot />
        </DialogContent>
    </Dialog>
</template>
