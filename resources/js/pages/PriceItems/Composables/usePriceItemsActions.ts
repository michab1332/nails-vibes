import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import admin from '@/routes/admin';
import type { PriceItem } from "@/types";

export const usePriceItemsActions = () => {
    const isDeleteDialogOpen = ref(false);
    const itemToDelete = ref<PriceItem | null>(null);

    const editRow = (item: PriceItem) => {
        router.visit(admin.priceItems.edit(item).url);
    }

    const askDeleteRow = (item: PriceItem) => {
        itemToDelete.value = item;
        isDeleteDialogOpen.value = true;
    }

    const confirmDelete = () => {
        if (itemToDelete.value) {
            router.delete(admin.priceItems.destroy(itemToDelete.value).url, {
                onSuccess: () => {
                    isDeleteDialogOpen.value = false;
                    itemToDelete.value = null;
                }
            });
        }
    }

    const createItem = () => {
        router.visit(admin.priceItems.create().url);
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
