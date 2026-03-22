import { h } from 'vue';
import type { ColumnDef } from '@tanstack/vue-table';
import type { Client } from '@/types';
import DataTableRowActions from './DataTableRowActions.vue';

export const columns: ColumnDef<Client>[] = [
    {
        accessorKey: 'name',
        header: 'Imię i nazwisko',
        cell: ({ row }) => h('div', { class: 'font-medium text-foreground' }, row.getValue('name')),
    },
    {
        accessorKey: 'ig_name',
        header: 'Instagram',
        cell: ({ row }) => h('div', { class: 'text-muted-foreground' }, row.getValue('ig_name') || '-'),
    },
    {
        accessorKey: 'phone_number',
        header: 'Numer telefonu',
        cell: ({ row }) => h('div', { class: 'text-muted-foreground' }, row.getValue('phone_number') || '-'),
    },
    {
        accessorKey: 'note',
        header: 'Notatka',
        cell: ({ row }) => h('div', { class: 'text-muted-foreground text-xs whitespace-pre-wrap max-w-[300px]' }, row.getValue('note') || '-'),
    },
    {
        id: 'actions',
        cell: ({ row, table }) => h(DataTableRowActions, {
            client: row.original,
            tableMeta: table.options.meta
        }),
    },
];
