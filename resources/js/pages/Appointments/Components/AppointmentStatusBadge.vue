<script setup lang="ts">
import { computed } from 'vue';
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import { AppointmentStatus } from '@/types';

const props = defineProps<{
    status: AppointmentStatus;
}>();

const variant = computed(() => {
    switch (props.status) {
        case AppointmentStatus.Scheduled:
            return 'default';
        case AppointmentStatus.Completed:
            return 'secondary';
        case AppointmentStatus.Cancelled:
            return 'destructive';
        case AppointmentStatus.NoShow:
            return 'outline';
        default:
            return 'default';
    }
});

const customClass = computed(() => {
    switch (props.status) {
        case AppointmentStatus.Scheduled:
            return 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300 border-purple-200 dark:border-purple-800';
        case AppointmentStatus.Completed:
            return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800';
        case AppointmentStatus.Cancelled:
            return 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300 border-rose-200 dark:border-rose-800';
        case AppointmentStatus.NoShow:
            return 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400 border-slate-200 dark:border-slate-700';
        default:
            return '';
    }
});
</script>

<template>
    <Badge :variant="variant" :class="cn('px-2.5 py-0.5 font-bold', customClass)">
        {{ status }}
    </Badge>
</template>
