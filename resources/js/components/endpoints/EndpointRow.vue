<script setup lang="ts">
import { TableCell, TableRow } from '@/components/ui/table';
import { CheckSquare, KeyRound, Pencil, ShieldOff, Square, Trash2, User } from 'lucide-vue-next';

defineProps(['endpoint']);

const emit = defineEmits(['edit', 'delete']);
</script>

<template>
    <TableRow :key="endpoint.id">
        <TableCell class="py-2 font-medium">
            {{ endpoint.name }}
        </TableCell>
        <TableCell class="py-2">{{ endpoint.url }}</TableCell>
        <TableCell class="py-2"><component :is="endpoint.dashboard_visible ? CheckSquare : Square" /></TableCell>
        <TableCell class="py-2">
            <component :is="endpoint.auth_type === 'basic' ? User : endpoint.auth_type === 'token' ? KeyRound : ShieldOff" />
        </TableCell>
        <TableCell class="py-2">{{ endpoint.method }}</TableCell>
        <TableCell class="py-2">{{ endpoint.check_interval }}</TableCell>
        <TableCell class="py-2">
            <button @click="emit('edit', endpoint)" class="mr-2 text-green-500"><component :is="Pencil" /></button>
            <button @click="emit('delete', endpoint)" class="text-red-500"><component :is="Trash2" /></button>
        </TableCell>
    </TableRow>
</template>
