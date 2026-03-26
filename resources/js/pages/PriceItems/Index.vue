<template>
    <Head title="Cennik" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Cennik usług</h1>
                    <p class="text-muted-foreground">Zarządzaj usługami i ich cenami w swoim salonie.</p>
                </div>
                <Button @click="createItem">
                    <Plus class="mr-2 h-4 w-4" />
                    Dodaj usługę
                </Button>
            </div>

            <DataTable
                :columns="columns"
                :data="props.data"
                :on-edit="editRow"
                :on-delete="askDeleteRow"
            />
        </div>

        <DeletePriceItemModal
            :item="itemToDelete"
            v-model:open="isDeleteDialogOpen"
            @confirm="confirmDelete"
        />
    </AppLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { usePriceItemsActions } from "@/pages/PriceItems/Composables/usePriceItemsActions";
import admin from '@/routes/admin';
import type { BreadcrumbItem, PriceItem } from '@/types';
import DeletePriceItemModal from './Components/DeletePriceItemModal.vue';
import { columns } from './Components/Table/columns';
import DataTable from './Components/Table/DataTable.vue';

const {
    editRow,
    askDeleteRow,
    confirmDelete,
    createItem,
    isDeleteDialogOpen,
    itemToDelete
} = usePriceItemsActions();

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
