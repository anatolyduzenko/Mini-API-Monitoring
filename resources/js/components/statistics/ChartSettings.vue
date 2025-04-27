<script setup lang="ts">
import { ToggleGroup, ToggleGroupItem } from '@/components/ui/toggle-group';
import { ref, watch } from 'vue';

const props = defineProps({
    options: {
        type: Array,
        required: true,
    },
    id: {
        type: String,
        default: 'settings',
    },
    modelValue: {
        type: String,
        default: '',
    },
    type: {
        type: String,
        default: 'single',
    },
});
const emit = defineEmits(['update:modelValue']);

const selected = ref(props.modelValue);

const switchId = `${props.id}-${Math.random().toString(36).slice(2, 10)}`;

watch(
    () => props.modelValue,
    (val) => {
        selected.value = val;
    },
);

watch(selected, (val) => {
    emit('update:modelValue', val);
});
</script>

<template>
    <div class="items-top flex space-x-2">
        <ToggleGroup :id="switchId" :type="type" v-model="selected" class="flex rounded-md border shadow-sm">
            <ToggleGroupItem v-for="option in options" :key="option.value" :value="option.value" class="h-8">
                {{ option.label }}
            </ToggleGroupItem>
        </ToggleGroup>
    </div>
</template>
