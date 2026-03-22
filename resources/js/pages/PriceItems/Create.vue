<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import type { BreadcrumbItem } from '@/types';
import Form from './Parts/Form.vue';

defineProps<{
    categories: string[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Cennik',
        href: admin.priceItems.index(),
    },
    {
        title: 'Dodaj usługę',
        href: admin.priceItems.create(),
    },
];
</script>

<template>
    <Head title="Dodaj usługę" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Dodaj nową usługę</h1>
                    <p class="text-muted-foreground">Uzupełnij dane nowej usługi w Twoim salonie.</p>
                </div>
            </div>

            <div class="rounded-xl border bg-card p-6">
                <Form :categories="categories" />
            </div>
        </div>
    </AppLayout>
</template>
