<script setup lang="ts">
import { TableCell, TableRow } from '@/components/ui/table';

defineProps(['endpoint']);
</script>

<template>
    <TableRow :key="endpoint.id">
        <TableCell class="p-2 font-medium">
            {{ endpoint.name }}
        </TableCell>
        <TableCell class="p-2">{{ endpoint.uptime }}</TableCell>
    </TableRow>
</template>
