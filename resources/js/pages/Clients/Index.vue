<template>
    <Head title="Klienci" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Klienci</h1>
                    <p class="text-muted-foreground">Zarządzaj bazą swoich klientek.</p>
                </div>
                <Button @click="createItem">
                    <Plus class="mr-2 h-4 w-4" />
                    Dodaj klienta
                </Button>
            </div>

            <DataTable
                :columns="columns"
                :data="props.data.data"
                :on-edit="editRow"
                :on-delete="askDeleteRow"
            />

            <div v-if="props.data.total > props.data.per_page" class="flex items-center justify-end px-2">
                <Pagination
                    v-slot="{ page }"
                    :total="props.data.total"
                    :sibling-count="1"
                    :show-edges="true"
                    :default-page="props.data.current_page"
                    :items-per-page="props.data.per_page"
                    @update:page="changePage"
                >
                    <PaginationContent v-slot="{ items }" class="flex items-center gap-1">
                        <PaginationFirst />
                        <PaginationPrevious />

                        <template v-for="(item, index) in items">
                            <PaginationItem v-if="item.type === 'page'" :key="index" :value="item.value" as-child>
                                <Button class="w-10 h-10 p-0" :variant="item.value === page ? 'default' : 'outline'">
                                    {{ item.value }}
                                </Button>
                            </PaginationItem>
                            <PaginationEllipsis v-else :key="item.type" :index="index" />
                        </template>

                        <PaginationNext />
                        <PaginationLast />
                    </PaginationContent>
                </Pagination>
            </div>
        </div>

        <DeleteClientModal
            :item="itemToDelete"
            v-model:open="isDeleteDialogOpen"
            @confirm="confirmDelete"
        />
    </AppLayout>
</template>

<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationFirst,
    PaginationItem,
    PaginationLast,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import DataTable from '@/pages/PriceItems/Components/Table/DataTable.vue';
import { columns } from './Components/Table/columns';
import type { BreadcrumbItem, Client } from '@/types';
import { useClientsActions } from "./Composables/useClientsActions";
import DeleteClientModal from './Components/DeleteClientModal.vue';

const {
    editRow,
    askDeleteRow,
    confirmDelete,
    createItem,
    isDeleteDialogOpen,
    itemToDelete
} = useClientsActions();

const props = defineProps<{
    data: {
        data: Client[];
        total: number;
        per_page: number;
        current_page: number;
        last_page: number;
        links: any[];
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Klienci',
        href: admin.clients.index(),
    },
];

const changePage = (page: number) => {
    router.visit(admin.clients.index({ query: { page } }).url, {
        preserveScroll: true,
        preserveState: true,
    });
};

</script>
