<script setup lang="ts">
import { TableCell, TableRow } from '@/components/ui/table';

defineProps(['endpoint']);

const emit = defineEmits(['edit', 'delete']);
</script>

<template>
    <TableRow :key="endpoint.id">
        <TableCell class="py-2 font-medium">
            {{ endpoint.name }}
        </TableCell>
        <TableCell class="py-2">{{ endpoint.url }}</TableCell>
        <TableCell class="py-2">{{ endpoint.auth_type }}</TableCell>
        <TableCell class="py-2">{{ endpoint.method }}</TableCell>
        <TableCell class="py-2">{{ endpoint.check_interval }}</TableCell>
        <TableCell class="py-2 text-right">
            <button @click="emit('edit', endpoint)" class="mr-2 text-green-500">✏️</button>
            <button @click="emit('delete', endpoint)" class="text-red-500">🗑️</button>
        </TableCell>
    </TableRow>
</template>
