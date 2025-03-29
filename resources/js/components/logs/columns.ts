import { ColumnDef } from '@tanstack/vue-table';
import { useDateFormat } from '@vueuse/core';
import { h } from 'vue';

export const columns: ColumnDef<[]>[] = [
    {
        accessorKey: 'id',
        header: () => h('div', { class: 'text-left font-bold' }, 'ID'),
        cell: ({ row }) => {
            const id = row.getValue('id');
            return h('div', { class: 'text-left font-medium' }, id);
        },
    },
    {
        accessorKey: 'endpoint_name',
        header: () => h('div', { class: 'text-left font-bold' }, 'Endpoint Name'),
        cell: ({ row }) => {
            const name = row.original.endpoint.name;
            return h('div', { class: 'text-left font-bold' }, name);
        },
    },
    {
        accessorKey: 'status_code',
        header: () => h('div', { class: 'text-center font-bold' }, 'Status Code'),
        cell: ({ row }) => {
            const statusCode = row.getValue('status_code');
            return h('div', { class: 'text-center font-medium' }, statusCode);
        },
    },
    {
        accessorKey: 'response_time',
        header: () => h('div', { class: 'text-center font-bold' }, 'Response Time(ms)'),
        cell: ({ row }) => {
            const responseTime = row.getValue('response_time');
            return h('div', { class: 'text-center font-medium' }, responseTime);
        },
    },
    {
        accessorKey: 'created_at',
        header: () => h('div', { class: 'text-center font-bold' }, 'Checked At'),
        cell: ({ row }) => {
            const checkedDateRaw = row.getValue('created_at');
            const checkedDate = useDateFormat(checkedDateRaw, 'MM-DD-YYYY HH:mm:ss');
            return h('div', { class: 'text-center font-medium' }, checkedDate.value);
        },
    },
];
