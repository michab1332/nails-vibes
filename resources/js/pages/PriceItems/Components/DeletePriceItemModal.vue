<script setup lang="ts">
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import type { PriceItem } from '@/types';

const props = defineProps<{
    item: PriceItem | null;
    open: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
    (e: 'confirm'): void;
}>();
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Czy na pewno chcesz usunąć tę usługę?</DialogTitle>
                <DialogDescription v-if="item">
                    Usługa <strong>{{ item.name }}</strong> zostanie trwale usunięta z cennika. Tej operacji nie można cofnąć.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="gap-2">
                <DialogClose as-child>
                    <Button variant="outline">Anuluj</Button>
                </DialogClose>
                <Button variant="destructive" @click="emit('confirm')">
                    Usuń usługę
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
