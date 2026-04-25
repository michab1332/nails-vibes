<script setup lang="ts">
/**
 * MonthView
 * 
 * Renders the 6x7 grid of days. Designed to be clean and iOS-like.
 */
import { inject, computed } from 'vue';
import { CALENDAR_CONTEXT_KEY } from '../context';
import { useCalendarMath } from '../composables/useCalendarMath';
import { format, startOfWeek, addDays } from 'date-fns';

const context = inject(CALENDAR_CONTEXT_KEY);
if (!context) throw new Error('MonthView must be used within SimpleCalendarRoot');

const { getDaysInMonthGrid } = useCalendarMath();

// Generate the 42 days for the grid
const days = computed(() => 
  getDaysInMonthGrid(
    context.state.currentDate, 
    context.state.selectedDate, 
    context.state.events, 
    context.state.config
  )
);

// Get week day names based on config (Mon, Tue, etc.)
const weekDays = computed(() => {
  const start = startOfWeek(new Date(), { weekStartsOn: context.state.config.weekStartsOn });
  return Array.from({ length: 7 }).map((_, i) => 
    format(addDays(start, i), 'EEEEE', { locale: context.state.config.locale })
  );
});

const handleDayClick = (date: Date) => {
  context.setSelectedDate(date);
};
</script>

<template>
  <div class="month-view flex flex-col h-full overflow-hidden select-none">
    <!-- Week Days Header -->
    <div class="grid grid-cols-7 border-b border-border/50">
      <div 
        v-for="dayName in weekDays" 
        :key="dayName" 
        class="py-2 text-center text-[10px] font-medium uppercase tracking-wider text-muted-foreground"
      >
        {{ dayName }}
      </div>
    </div>

    <!-- Days Grid -->
    <div class="grid grid-cols-7 flex-1">
      <div 
        v-for="day in days" 
        :key="day.date.toISOString()"
        class="relative min-h-[60px] border-b border-r border-border/30 last:border-r-0 cursor-pointer active:bg-accent/50 transition-colors"
        @click="handleDayClick(day.date)"
      >
        <slot name="day" v-bind="day">
          <!-- Default Day Cell (iOS Style) -->
          <div 
            class="flex flex-col items-center justify-center pt-2 gap-1 w-full h-full"
            :class="{ 'bg-accent/20': !day.isCurrentMonth }"
          >
            <span 
              class="text-sm font-medium w-8 h-8 flex items-center justify-center rounded-full transition-all"
              :class="[
                day.isToday ? 'bg-primary text-primary-foreground' : '',
                day.isSelected && !day.isToday ? 'bg-accent text-accent-foreground border-2 border-primary' : '',
                !day.isCurrentMonth ? 'text-muted-foreground/40' : 'text-foreground'
              ]"
            >
              {{ format(day.date, 'd') }}
            </span>

            <!-- Event Indicators (Dots) -->
            <div class="flex gap-0.5 min-h-[4px] mt-0.5">
              <div 
                v-for="(event, idx) in day.events.slice(0, 3)" 
                :key="event.id"
                class="w-1 h-1 rounded-full bg-primary/60"
              />
              <div v-if="day.events.length > 3" class="w-1 h-1 rounded-full bg-muted-foreground/40" />
            </div>
          </div>
        </slot>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Ensure the grid doesn't have double borders */
.month-view > .grid:last-child {
  border-right: 1px solid transparent;
}
</style>
