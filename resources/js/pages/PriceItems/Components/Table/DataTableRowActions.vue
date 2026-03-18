<script setup lang="ts">
import { MoreHorizontal, Pencil, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { PriceItem } from '@/types';

defineProps<{
    priceItem: PriceItem;
    tableMeta: any,
}>();
</script>

<template>
    <div class="flex items-center justify-end">
        <DropdownMenu>
            <DropdownMenuTrigger as-child>
                <Button variant="ghost" class="h-8 w-8 p-0 focus-visible:ring-0 focus-visible:ring-offset-0">
                    <span class="sr-only">Otwórz menu</span>
                    <MoreHorizontal class="h-4 w-4" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" class="w-[160px]">
                <DropdownMenuLabel>Akcje</DropdownMenuLabel>
                <DropdownMenuSeparator />
                <DropdownMenuItem @click="tableMeta?.edit(priceItem)">
                    <Pencil class="mr-2 h-4 w-4" />
                    <span>Edytuj</span>
                </DropdownMenuItem>
                <DropdownMenuItem @click="tableMeta?.delete(priceItem)" class="text-destructive focus:text-destructive">
                    <Trash2 class="mr-2 h-4 w-4" />
                    <span>Usuń</span>
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    </div>
</template>
