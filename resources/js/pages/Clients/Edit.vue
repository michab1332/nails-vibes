<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import type { BreadcrumbItem, Client } from '@/types';
import Form from './Parts/Form.vue';

const props = defineProps<{
    client: Client;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Klienci',
        href: admin.clients.index(),
    },
    {
        title: 'Edytuj klienta',
        href: admin.clients.edit(props.client),
    },
];
</script>

<template>
    <Head :title="`Edytuj ${client.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Edytuj klienta</h1>
                    <p class="text-muted-foreground">Zaktualizuj dane klienta {{ client.name }}.</p>
                </div>
            </div>

            <div class="rounded-xl border bg-card p-6">
                <Form :client="client" />
            </div>
        </div>
    </AppLayout>
</template>
