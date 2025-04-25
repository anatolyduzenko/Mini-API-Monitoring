<script setup lang="ts">
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { computed } from 'vue';

const switchId = `switch-${Math.random().toString(36).slice(2, 10)}`;

const props = defineProps({
    enabled: {
        type: Boolean,
        default: false,
    },
    label: {
        type: String,
        default: 'Label',
    },
});

const emit = defineEmits(['update:enabled']);

const isOn = computed({
    get: () => props.enabled,
    set: (value) => emit('update:enabled', value),
});
</script>

<template>
    <div class="flex items-center space-x-2">
        <Label :for="switchId">{{ label }}</Label>
        <Switch :id="switchId" v-model="isOn" />
    </div>
</template>
