<script setup lang="ts">
/**
 * SimpleCalendarGrid
 * 
 * Switches between different calendar views (Month, Day)
 * based on the current state. It passes down slots for deep customization.
 */
import { inject } from 'vue';
import { CALENDAR_CONTEXT_KEY } from '../context';
import MonthView from '../views/MonthView.vue';
import DayView from '../views/DayView.vue';

const context = inject(CALENDAR_CONTEXT_KEY);
if (!context) {
  throw new Error('SimpleCalendarGrid must be used within SimpleCalendarRoot');
}
</script>

<template>
  <div class="simple-calendar-grid flex-1 min-h-0">
    <!-- Month View -->
    <MonthView v-if="context.state.viewMode === 'month'">
      <!-- Transparently pass slots from Parent to MonthView -->
      <template #day="dayProps">
        <slot name="day" v-bind="dayProps" />
      </template>
      <template #event="eventProps">
        <slot name="event" v-bind="eventProps" />
      </template>
    </MonthView>

    <!-- Day View -->
    <DayView v-else-if="context.state.viewMode === 'day'">
      <template #hour="hourProps">
        <slot name="hour" v-bind="hourProps" />
      </template>
    </DayView>
  </div>
</template>
