<script setup lang="ts">
import { onMounted, onBeforeUnmount, h } from 'vue';
import { MessageSquare, MessageSquareDot, MessageSquareWarning } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

const props = defineProps<{
    channel: string;
    event: Array<string>|string;
    showToast?: boolean;
    toastOptions?: {
        title?: string;
        duration?: number;
        icon?: string;
    };
}>();

const iconMap: Record<string, any> = {
    warning: MessageSquareWarning,
    success: MessageSquareDot,
    info: MessageSquare,
    error: MessageSquareWarning,
    };

let channelInstance: ReturnType<typeof window.Echo.channel>;

onMounted(() => {
    channelInstance = window.Echo.channel(props.channel);

    channelInstance.listenToAll((eventName: string, e: any) => {
        const events = Array.isArray(props.event) ? props.event : [props.event];
        
        if (events.includes(eventName)) {
        
            if (props.showToast) {
                const iconComponent = iconMap[e.level] ?? MessageSquare;

                toast(props.toastOptions?.title ?? e.title, {
                    style: {
                        background: e.background ?? 'crimson'
                        
                    },
                    classes: {
                        content: 'ml-2',
                    },
                    description: e.message ?? JSON.stringify(e),
                    duration: props.toastOptions?.duration ?? 500000,
                    icon: h(iconComponent,  { class: 'w-6 h-6 mr-2 text-current' }),
                });
            }
        }
    });
});

onBeforeUnmount(() => {
  if (channelInstance) {
    window.Echo.leave(props.channel);
  }
});
</script>

<template>
  <!-- This component is invisible. It just listens. -->
  <div style="display: none;"></div>
</template>
