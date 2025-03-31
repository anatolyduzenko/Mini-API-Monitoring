<script setup lang="ts">
import { TableCell, TableRow } from '@/components/ui/table';
import { EndpointStatRecord } from '@/types/app/endpointstatrecord';

const props = defineProps<{
    endpoint: EndpointStatRecord;
}>();
</script>

<template>
    <TableRow :key="endpoint.id">
        <TableCell class="p-2 text-left font-medium">
            {{ endpoint.name }}
        </TableCell>
        <TableCell class="p-2">{{ endpoint.uptime }}</TableCell>
    </TableRow>
</template>
