import { h } from 'vue';
import type { ColumnDef } from '@tanstack/vue-table';
import type { PriceItem } from '@/types';
import { Badge } from '@/components/ui/badge';
import DataTableRowActions from './DataTableRowActions.vue';

export const columns: ColumnDef<PriceItem>[] = [
    {
        accessorKey: 'name',
        header: 'Nazwa usługi',
        cell: ({ row }) => h('div', { class: 'font-medium text-foreground' }, row.getValue('name')),
    },
    {
        accessorKey: 'category',
        header: 'Kategoria',
        cell: ({ row }) => {
            const category = row.getValue('category') as string;
            return h(Badge, { variant: 'outline', class: 'font-normal text-xs' }, {
                default: () => category
            });
        },
    },
    {
        accessorKey: 'formatted_price',
        header: () => h('div', { class: 'text-right' }, 'Cena'),
        cell: ({ row }) => {
            const price = row.getValue('formatted_price') as string;
            return h('div', { class: 'text-right font-semibold text-sm' }, price);
        },
    },
    {
        id: 'actions',
        cell: ({ row, table }) => h(DataTableRowActions, {
            priceItem: row.original,
            tableMeta: table.options.meta
        }),
    },
];
