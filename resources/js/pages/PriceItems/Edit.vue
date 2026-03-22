<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import type { BreadcrumbItem, PriceItem } from '@/types';
import Form from './Parts/Form.vue';

const props = defineProps<{
    priceItem: PriceItem;
    categories: string[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Cennik',
        href: admin.priceItems.index(),
    },
    {
        title: 'Edytuj usługę',
        href: admin.priceItems.edit(props.priceItem),
    },
];
</script>

<template>
    <Head :title="`Edytuj ${priceItem.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Edytuj usługę</h1>
                    <p class="text-muted-foreground">Zaktualizuj dane usługi {{ priceItem.name }}.</p>
                </div>
            </div>

            <div class="rounded-xl border bg-card p-6">
                <Form :price-item="priceItem" :categories="categories" />
            </div>
        </div>
    </AppLayout>
</template>
