<script setup lang="ts">
import { TableCell, TableRow } from '@/components/ui/table';

defineProps(['endpoint']);
</script>

<template>
    <TableRow :key="endpoint.id">
        <TableCell class="p-2 text-left">
            {{ endpoint.name }}
        </TableCell>
        <TableCell class="p-2">{{ endpoint.status_code }}</TableCell>
        <TableCell class="p-2">{{ endpoint.response_time }}</TableCell>
        <TableCell class="p-2">{{ endpoint.created_at }}</TableCell>
    </TableRow>
</template>
