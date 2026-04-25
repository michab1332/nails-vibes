<script setup lang="ts">
/**
 * AppointmentStatusBadge
 * 
 * A simple wrapper around Shadcn Badge to display appointment statuses
 * with appropriate colors.
 */
import { Badge } from '@/components/ui/badge';
import { AppointmentStatus } from '@/types/appointment';

const props = defineProps<{
  status: AppointmentStatus | string;
}>();

const variants: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
  [AppointmentStatus.Scheduled]: 'default',
  [AppointmentStatus.Completed]: 'secondary',
  [AppointmentStatus.Cancelled]: 'destructive',
  [AppointmentStatus.NoShow]: 'outline',
};

const variant = variants[props.status as string] || 'outline';
</script>

<template>
  <Badge :variant="variant" class="whitespace-nowrap">
    {{ status }}
  </Badge>
</template>
