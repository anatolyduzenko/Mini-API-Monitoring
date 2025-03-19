<script setup lang="ts">
defineProps(['endpoints']);

const emit = defineEmits(['edit', 'delete']);
</script>

<template>
    <div v-for="endpoint in endpoints.data" :key="endpoint.id" class="mx-4 rounded-lg border border-gray-200 p-4 shadow-sm">
        <p class="w-full overflow-hidden"><strong>Name:</strong> {{ endpoint.name }}</p>
        <p><strong>Method:</strong> {{ endpoint.method }}</p>
        <p>
            <button @click="emit('edit', endpoint)" class="mr-2 text-green-500">✏️</button>
            <button @click="emit('delete', endpoint)" class="text-red-500">🗑️</button>
        </p>
    </div>
</template>
