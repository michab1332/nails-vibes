<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import DataTable from './Components/Table/DataTable.vue';
import { columns } from './Components/Table/columns';
import type { BreadcrumbItem, PriceItem } from '@/types';
import { usePriceItemsActions } from "@/pages/PriceItems/Composables/usePriceItemsActions";

const { editRow, deleteRow } = usePriceItemsActions();

const props = defineProps<{
    data: PriceItem[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Cennik',
        href: admin.priceItems.index(),
    },
];

</script>

<template>
    <Head title="Cennik" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Cennik usług</h1>
                    <p class="text-muted-foreground">Zarządzaj usługami i ich cenami w swoim salonie.</p>
                </div>
            </div>

            <DataTable
                :columns="columns"
                :data="props.data"
                :on-edit="editRow"
                :on-delete="deleteRow"
            />
        </div>
    </AppLayout>
</template>
