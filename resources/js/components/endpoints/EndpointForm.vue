<script setup lang="ts">
import { ref, watch } from 'vue';

const props = defineProps(['show', 'endpoint', 'currentUser']);
const emit = defineEmits(['close', 'submit']);

const editableEndpoint = ref({
    id: null,
    name: '',
    url: '',
    method: 'GET',
    check_interval: 5,
    alert_threshold: 90,
    user_id: props.currentUser ? props.currentUser.id : null,
});
const errors = ref({ name: '', url: '' });

watch(
    () => props.endpoint,
    (newValue) => {
        editableEndpoint.value = newValue
            ? { ...newValue, user_id: props.currentUser ? props.currentUser.id : null }
            : {
                  id: null,
                  name: '',
                  url: '',
                  method: 'GET',
                  check_interval: 5,
                  alert_threshold: 90,
                  user_id: props.currentUser ? props.currentUser.id : null,
              };
    },
    { immediate: true },
);

const submitForm = () => {
    emit('submit', editableEndpoint.value);
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
        <div class="w-96 rounded bg-white p-6 shadow-lg">
            <h3 class="mb-4 text-lg font-bold">{{ editableEndpoint.id ? 'Edit Endpoint' : 'Add New Endpoint' }}</h3>

            <label class="mb-2 block">Name:</label>
            <p v-if="errors.name" class="error">{{ errors.name[0] }}</p>
            <input v-model="editableEndpoint.name" type="text" class="mb-3 w-full rounded border p-2" />

            <label class="mb-2 block">URL:</label>
            <p v-if="errors.url" class="error">{{ errors.url[0] }}</p>
            <input v-model="editableEndpoint.url" type="text" class="mb-3 w-full rounded border p-2" />

            <label class="mb-2 block">Method:</label>
            <select v-model="editableEndpoint.method" class="mb-3 w-full rounded border p-2">
                <option value="GET">GET</option>
                <option value="POST">POST</option>
                <option value="PUT">PUT</option>
                <option value="DELETE">DELETE</option>
            </select>

            <label class="mb-2 block">Check Interval (minutes):</label>
            <input v-model="editableEndpoint.check_interval" type="number" class="mb-3 w-full rounded border p-2" />

            <label class="mb-2 block">Alert threshold (percent):</label>
            <input v-model="editableEndpoint.alert_threshold" type="number" class="mb-3 w-full rounded border p-2" />

            <div class="mt-4 flex justify-between">
                <button @click="emit('close')" class="rounded bg-gray-400 px-4 py-2 text-white">Cancel</button>
                <button @click="submitForm" class="rounded bg-blue-500 px-4 py-2 text-white">
                    {{ editableEndpoint.id ? 'Update' : 'Create' }}
                </button>
            </div>
        </div>
    </div>
</template>
