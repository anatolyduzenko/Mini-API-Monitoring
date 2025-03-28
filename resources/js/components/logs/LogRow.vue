<script setup lang="ts">
import { TableCell, TableRow } from '@/components/ui/table';
import { useDateFormat, useNow } from '@vueuse/core'

defineProps(['record']);

</script>

<template>
    <TableRow :key="record.id" class="">
        <TableCell class="px-4 py-2 font-medium">
            {{ record.endpoint.name }}
        </TableCell>
        <TableCell class="px-4 py-2">{{ record.status_code }}</TableCell>
        <TableCell class="px-4 py-2">{{ record.response_time }}</TableCell>
        <TableCell class="px-4 py-2">{{ useDateFormat(record.created_at, 'DD-MM-YYYY HH:mm:ss') }}</TableCell>
    </TableRow>
</template>
