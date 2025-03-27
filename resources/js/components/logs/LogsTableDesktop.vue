<script setup lang="ts">
import LogRow from '@/components/logs/LogRow.vue';
import { Table, TableBody, TableCaption, TableHead, TableHeader, TableRow } from '@/components/ui/table';

defineProps(['logs']);

</script>

<template>
    <div class="px-5">
        <Table class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200">
            <TableCaption class="text-gray-700 dark:text-gray-300">API Checking Logs.</TableCaption>
            <TableHeader class="bg-gray-100 dark:bg-gray-800">
                <TableRow class="border-b border-gray-300 dark:border-gray-700">
                    <TableHead class="py-2 px-4 text-left text-gray-800 dark:text-gray-300">Endpoint Name</TableHead>
                    <TableHead class="py-2 px-4 text-left text-gray-800 dark:text-gray-300">Status Code</TableHead>
                    <TableHead class="py-2 px-4 text-left text-gray-800 dark:text-gray-300">Response Time(ms)</TableHead>
                    <TableHead class="py-2 px-4 text-left text-gray-800 dark:text-gray-300">Checked At</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody class="divide-y divide-gray-300 dark:divide-gray-700">
                <LogRow 
                    v-for="record in logs.data" 
                    :key="record.id" 
                    :record="record" 
                    class="hover:bg-gray-100 dark:hover:bg-gray-800"
                />
            </TableBody>
        </Table>
    </div>
</template>