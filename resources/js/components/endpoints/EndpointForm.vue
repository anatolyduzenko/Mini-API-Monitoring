<script setup lang="ts">
    import { ref, watch } from "vue";

    const props = defineProps(["show", "endpoint", "currentUser"]);
    const emit = defineEmits(["close", "submit"]);

    const editableEndpoint = ref({ 
        id: null, 
        name: "", 
        url: "", 
        method: "GET", 
        check_interval: 5, 
        user_id: props.currentUser ? props.currentUser.id : null
    });
    const errors = ref({ name: "", url: ""});

    watch(
        () => props.endpoint,
        (newValue) => {
            editableEndpoint.value = newValue 
            ? { ...newValue, user_id: props.currentUser ? props.currentUser.id : null } 
            : { id: null, name: "", url: "", method: "GET", check_interval: 5, user_id: props.currentUser ? props.currentUser.id : null };
        },
        { immediate: true }
    );

    const submitForm = () => {
        emit("submit", editableEndpoint.value);
    };
</script>

<template>
    <div v-if="show" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded shadow-lg w-96">
            <h3 class="text-lg font-bold mb-4">{{ editableEndpoint.id ? "Edit Endpoint" : "Add New Endpoint" }}</h3>

            <label class="block mb-2">Name:</label>
            <p v-if="errors.name" class="error">{{ errors.name[0] }}</p>
            <input v-model="editableEndpoint.name" type="text" class="w-full border p-2 rounded mb-3" />

            <label class="block mb-2">URL:</label>
            <p v-if="errors.url" class="error">{{ errors.url[0] }}</p>
            <input v-model="editableEndpoint.url" type="text" class="w-full border p-2 rounded mb-3" />

            <label class="block mb-2">Method:</label>
            <select v-model="editableEndpoint.method" class="w-full border p-2 rounded mb-3">
                <option value="GET">GET</option>
                <option value="POST">POST</option>
                <option value="PUT">PUT</option>
                <option value="DELETE">DELETE</option>
            </select>

            <label class="block mb-2">Check Interval (minutes):</label>
            <input v-model="editableEndpoint.check_interval" type="number" class="w-full border p-2 rounded mb-3" />

            <p class="text-sm text-gray-600">Current User: {{ props.currentUser?.name }}</p>
            
            <div class="flex justify-between mt-4">
                <button @click="emit('close')" class="bg-gray-400 text-white px-4 py-2 rounded">Cancel</button>
                <button @click="submitForm" class="bg-blue-500 text-white px-4 py-2 rounded">
                    {{ editableEndpoint.id ? "Update" : "Create" }}
                </button>
            </div>
        </div>
    </div>
</template>
