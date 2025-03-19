<script setup lang="ts">
import { TableCell, TableRow } from '@/components/ui/table';

defineProps(['endpoint']);

const emit = defineEmits(['edit', 'delete']);
</script>

<template>
    <TableRow :key="endpoint.id">
        <TableCell class="font-medium">
            {{ endpoint.name }}
        </TableCell>
        <TableCell>{{ endpoint.url }}</TableCell>
        <TableCell>{{ endpoint.method }}</TableCell>
        <TableCell>{{ endpoint.check_interval }}</TableCell>
        <TableCell class="text-right">
            <button @click="emit('edit', endpoint)" class="mr-2 text-green-500">✏️</button>
            <button @click="emit('delete', endpoint)" class="text-red-500">🗑️</button>
        </TableCell>
    </TableRow>
</template>
