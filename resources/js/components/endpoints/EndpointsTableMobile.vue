<script setup lang="ts">

    defineProps(["endpoints"]);
    
    const emit = defineEmits(["edit", "delete"]);
</script>

<template>
    <div v-for="endpoint in endpoints.data" :key="endpoint.id" class="border border-gray-200 rounded-lg p-4 mx-4 shadow-sm">
        <p class="w-full overflow-hidden"><strong>Name:</strong> {{ endpoint.name }}</p>
        <p><strong>Method:</strong> {{ endpoint.method }}</p>
        <p>
            <button @click="emit('edit', endpoint)" class="text-green-500 mr-2">✏️</button>
            <button @click="emit('delete', endpoint)" class="text-red-500">🗑️</button>
        </p>
    </div>
</template>