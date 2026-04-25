<script setup lang="ts">
/**
 * SimpleCalendarHeader
 * 
 * A headless component that provides navigation functionality
 * (next, prev, today) and the current period label through slots.
 */
import { inject, computed } from 'vue';
import { CALENDAR_CONTEXT_KEY } from '../context';
import { useCalendarMath } from '../composables/useCalendarMath';

const context = inject(CALENDAR_CONTEXT_KEY);
if (!context) {
  throw new Error('SimpleCalendarHeader must be used within SimpleCalendarRoot');
}

const { getPeriodLabel } = useCalendarMath();

const label = computed(() => 
  getPeriodLabel(
    context.state.currentDate, 
    context.state.viewMode, 
    context.state.config.locale
  )
);

const goToToday = () => {
  context.setCurrentDate(new Date());
  context.setSelectedDate(new Date());
};
</script>

<template>
  <slot 
    :next="context.next" 
    :prev="context.prev" 
    :today="goToToday" 
    :label="label"
    :current-view="context.state.viewMode"
    :set-view="context.setViewMode"
  />
</template>
