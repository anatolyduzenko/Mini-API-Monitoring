<script setup lang="ts">
import { TableCell, TableRow } from '@/components/ui/table';

defineProps(['endpoint']);
</script>

<template>
    <TableRow :key="endpoint.id">
        <TableCell class="font-medium">
            {{ endpoint.name }}
        </TableCell>
        <TableCell>{{ endpoint.uptime }}</TableCell>
    </TableRow>
</template>
