import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import type { Client } from "@/types";
import admin from '@/routes/admin';

export const useClientsActions = () => {
    const isDeleteDialogOpen = ref(false);
    const itemToDelete = ref<Client | null>(null);

    const editRow = (item: Client) => {
        router.visit(admin.clients.edit(item).url);
    }

    const askDeleteRow = (item: Client) => {
        itemToDelete.value = item;
        isDeleteDialogOpen.value = true;
    }

    const confirmDelete = () => {
        if (itemToDelete.value) {
            router.delete(admin.clients.destroy(itemToDelete.value).url, {
                onSuccess: () => {
                    isDeleteDialogOpen.value = false;
                    itemToDelete.value = null;
                }
            });
        }
    }

    const createItem = () => {
        router.visit(admin.clients.create().url);
    }

    return {
        editRow,
        askDeleteRow,
        confirmDelete,
        createItem,
        isDeleteDialogOpen,
        itemToDelete
    };
}
