<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import type { BreadcrumbItem } from '@/types';
import Form from './Parts/Form.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Klienci',
        href: admin.clients.index(),
    },
    {
        title: 'Dodaj klienta',
        href: admin.clients.create(),
    },
];
</script>

<template>
    <Head title="Dodaj klienta" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Dodaj nowego klienta</h1>
                    <p class="text-muted-foreground">Uzupełnij dane nowego klienta.</p>
                </div>
            </div>

            <div class="rounded-xl border bg-card p-6">
                <Form />
            </div>
        </div>
    </AppLayout>
</template>
