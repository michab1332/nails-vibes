<script setup lang="ts">
/**
 * MonthView
 * 
 * Renders the 6x7 grid of days. Designed to be clean and iOS-like.
 * Includes native-feeling long press (hold) interactions for mobile.
 */
import { inject, computed, ref } from 'vue';
import { CALENDAR_CONTEXT_KEY } from '../context';
import { useCalendarMath } from '../composables/useCalendarMath';
import { format, startOfWeek, addDays } from 'date-fns';
import { onLongPress } from '@vueuse/core';

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

// Visual Feedback State for Long Press
const pressingDate = ref<string | null>(null);

const setupLongPress = (el: any, date: Date) => {
  if (el) {
    onLongPress(el, () => {
      // Trigger the actual hold action
      context.onDateHold(date);
      // Provide a tiny haptic-like bump by briefly resetting the state
      pressingDate.value = null; 
    }, { 
      delay: 400, // 400ms feels responsive on mobile
      modifiers: { stop: true, prevent: true },
      onStart: () => {
        // User touched the cell, start the visual "pressing down" animation
        pressingDate.value = date.toISOString();
      },
      onFinish: () => {
        // User lifted finger or started scrolling (cancelled)
        pressingDate.value = null;
      }
    });
  }
};
</script>

<template>
  <!-- Added touch-action specific classes to prevent browser interference during press -->
  <div class="month-view flex flex-col h-full overflow-hidden select-none">
    <!-- Week Days Header -->
    <div class="grid grid-cols-7 border-b border-border/50 bg-muted/10">
      <div 
        v-for="dayName in weekDays" 
        :key="dayName" 
        class="py-2 text-center text-[10px] font-bold uppercase tracking-wider text-muted-foreground"
      >
        {{ dayName }}
      </div>
    </div>

    <!-- Days Grid -->
    <div class="grid grid-cols-7 flex-1">
      <div 
        v-for="day in days" 
        :key="day.date.toISOString()"
        :ref="(el) => setupLongPress(el, day.date)"
        class="day-cell relative min-h-[60px] border-b border-r border-border/30 last:border-r-0 cursor-pointer transition-all duration-200 ease-out flex items-center justify-center"
        :class="[
          pressingDate === day.date.toISOString() 
            ? 'scale-90 bg-accent/40 rounded-lg shadow-inner z-10' 
            : 'active:bg-accent/20 scale-100'
        ]"
        @click="handleDayClick(day.date)"
      >
        <div class="absolute inset-0 w-full h-full pointer-events-none">
          <slot name="day" v-bind="day">
            <!-- Default Day Cell (iOS Style) -->
            <div 
              class="flex flex-col items-center justify-center pt-2 gap-1 w-full h-full"
              :class="{ 'bg-muted/5': !day.isCurrentMonth }"
            >
              <span 
                class="text-sm font-medium w-8 h-8 flex items-center justify-center rounded-full transition-colors duration-200"
                :class="[
                  day.isToday ? 'bg-primary text-primary-foreground shadow-sm' : '',
                  day.isSelected && !day.isToday ? 'bg-accent text-accent-foreground border-2 border-primary/40' : '',
                  !day.isCurrentMonth ? 'text-muted-foreground/30' : 'text-foreground'
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
  </div>
</template>

<style scoped>
/* 
  Mobile Optimizations 
  These rules prevent the OS from showing magnifying glasses, 
  copy/paste menus, or highlight colors when holding a finger down.
*/
.month-view {
  -webkit-touch-callout: none; /* iOS Safari */
    -webkit-user-select: none; /* Safari */
     -khtml-user-select: none; /* Konqueror HTML */
       -moz-user-select: none; /* Old versions of Firefox */
        -ms-user-select: none; /* Internet Explorer/Edge */
            user-select: none; /* Non-prefixed version */
}

/* Hardware acceleration for the scale animation */
.day-cell {
  will-change: transform, background-color;
  -webkit-tap-highlight-color: transparent; /* Remove Android tap highlight */
}

.month-view > .grid:last-child {
  border-right: 1px solid transparent;
}
</style>
