<script setup lang="ts">
/**
 * DayView (Hourly Timeline)
 * 
 * A skeleton for an hourly view. Users can use the #hour slot
 * to build custom timeline layouts.
 */
import { inject, computed } from 'vue';
import { CALENDAR_CONTEXT_KEY } from '../context';
import { format, startOfDay, addHours, isSameDay } from 'date-fns';

const context = inject(CALENDAR_CONTEXT_KEY);
if (!context) throw new Error('DayView must be used within SimpleCalendarRoot');

// Generate 24 hours for the day view
const hours = computed(() => {
  const dayStart = startOfDay(context.state.currentDate);
  return Array.from({ length: 24 }).map((_, i) => addHours(dayStart, i));
});

const dayEvents = computed(() => 
  context.state.events.filter(e => isSameDay(e.start, context.state.currentDate))
);
</script>

<template>
  <div class="day-view flex flex-col h-full overflow-y-auto">
    <div 
      v-for="hour in hours" 
      :key="hour.toISOString()"
      class="flex border-b border-border/30 min-h-[60px]"
    >
      <!-- Time Column -->
      <div class="w-16 flex-shrink-0 flex justify-end pr-3 pt-2 text-[11px] font-medium text-muted-foreground uppercase">
        {{ format(hour, 'HH:mm') }}
      </div>

      <!-- Content Column -->
      <div class="flex-1 relative bg-accent/5">
        <slot name="hour" :hour="hour" :events="dayEvents">
          <!-- Placeholder for events in this hour slot -->
        </slot>
      </div>
    </div>
  </div>
</template>
