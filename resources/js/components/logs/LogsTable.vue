<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { route } from 'ziggy-js';
import { TailwindPagination } from 'laravel-vue-pagination';
import LogsTableDesktop from '@/components/logs/LogsTableDesktop.vue';

const logs = ref([]);

const fetchLogs = async (page = 1) => {
    try {
        const response = await fetch(route('api.logs.index', { page: page }));
        logs.value = await response.json();
    } catch (error) {
        console.error('Error fetching logs:', error);
    }
};

onMounted(() => {
    fetchLogs();
});
</script>

<template>
    <div>
        <div class="p-4">
            <TailwindPagination :data="logs" @pagination-change-page="fetchLogs" />
        </div>
        <div class="hidden md:block">
            <LogsTableDesktop :logs="logs" />
        </div>

        <div class="block space-y-4 md:hidden">
            <!-- <LogsTableMobile :logs="logs" /> -->
        </div>

    </div>
</template>
