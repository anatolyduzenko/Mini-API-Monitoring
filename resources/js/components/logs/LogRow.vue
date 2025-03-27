<script setup lang="ts">
import { TableCell, TableRow } from '@/components/ui/table';

defineProps(['record']);

</script>

<template>
    <TableRow :key="record.id" class="">
        <TableCell class="px-4 py-2 font-medium">
            {{ record.endpoint.name }}
        </TableCell>
        <TableCell class="px-4 py-2">{{ record.status_code }}</TableCell>
        <TableCell class="px-4 py-2">{{ record.response_time }}</TableCell>
        <TableCell class="px-4 py-2">{{ record.created_at }}</TableCell>
    </TableRow>
</template>
