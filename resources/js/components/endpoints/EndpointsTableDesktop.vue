<script setup lang="ts">
import EndpointRow from '@/components/endpoints/EndpointRow.vue';
import { Table, TableBody, TableCaption, TableHead, TableHeader, TableRow } from '@/components/ui/table';

defineProps(['endpoints']);

const emit = defineEmits(['edit', 'delete']);

const openEditForm = (endpoint) => {
    emit('edit', endpoint);
};

const deleteEndpoint = (endpoint) => {
    emit('delete', endpoint);
};
</script>

<template>
    <div class="px-5">
        <Table class="w-full border border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
            <TableCaption class="text-gray-700 dark:text-gray-300">A list of your monitored endpoints.</TableCaption>
            <TableHeader class="bg-gray-100 dark:bg-gray-800">
                <TableRow class="border-b border-gray-300 dark:border-gray-700">
                    <TableHead class="px-4 py-2 text-left text-gray-800 dark:text-gray-300">Name</TableHead>
                    <TableHead class="px-4 py-2 text-left text-gray-800 dark:text-gray-300">URL</TableHead>
                    <TableHead class="px-4 py-2 text-left text-gray-800 dark:text-gray-300">Method</TableHead>
                    <TableHead class="px-4 py-2 text-left text-gray-800 dark:text-gray-300">Interval(min)</TableHead>
                    <TableHead class="px-4 py-2 text-right text-gray-800 dark:text-gray-300">Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody class="divide-y divide-gray-300 dark:divide-gray-700">
                <EndpointRow
                    v-for="endpoint in endpoints.data"
                    :key="endpoint.id"
                    :endpoint="endpoint"
                    @edit="openEditForm"
                    @delete="deleteEndpoint"
                    class="hover:bg-gray-100 dark:hover:bg-gray-800"
                />
            </TableBody>
        </Table>
    </div>
</template>
