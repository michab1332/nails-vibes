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
import type { Client } from '@/types';

defineProps<{
    item: Client | null;
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
                <DialogTitle>Czy na pewno chcesz usunąć tego klienta?</DialogTitle>
                <DialogDescription v-if="item">
                    Klient <strong>{{ item.name }}</strong> zostanie trwale usunięty. Tej operacji nie można cofnąć.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="gap-2">
                <DialogClose as-child>
                    <Button variant="outline">Anuluj</Button>
                </DialogClose>
                <Button variant="destructive" @click="emit('confirm')">
                    Usuń klienta
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
