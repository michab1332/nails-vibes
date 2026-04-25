<script setup lang="ts">
/**
 * SimpleCalendarRoot
 * 
 * The main container component that initializes the calendar state
 * and provides it to all child components via Provide/Inject.
 */
import { provide, watch } from 'vue';
import { CALENDAR_CONTEXT_KEY } from '../context';
import { useCalendarState } from '../composables/useCalendarState';
import { CalendarEvent, CalendarViewMode, CalendarConfig } from '../types';

interface Props {
  modelValue?: Date | null;
  events?: CalendarEvent[];
  viewMode?: CalendarViewMode;
  config?: Partial<CalendarConfig>;
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: null,
  events: () => [],
  viewMode: 'month',
});

const emit = defineEmits<{
  (e: 'update:modelValue', date: Date): void;
  (e: 'update:viewMode', mode: CalendarViewMode): void;
  (e: 'dateSelect', date: Date): void;
}>();

// Initialize the reactive state
const { state, setCurrentDate, setSelectedDate, setViewMode, next, prev } = useCalendarState({
  selectedDate: props.modelValue,
  events: props.events,
  viewMode: props.viewMode,
  config: props.config,
});

// Update state when props change (External -> Internal)
watch(() => props.modelValue, (newDate) => {
  if (newDate) setSelectedDate(newDate);
});

watch(() => props.events, (newEvents) => {
  state.events = newEvents;
}, { deep: true });

watch(() => props.viewMode, (newMode) => {
  setViewMode(newMode);
});

// Notify parent when internal state changes (Internal -> External)
watch(() => state.selectedDate, (newDate) => {
  if (newDate) {
    emit('update:modelValue', newDate);
    emit('dateSelect', newDate);
  }
});

watch(() => state.viewMode, (newMode) => {
  emit('update:viewMode', newMode);
});

// Provide context to all sub-components
provide(CALENDAR_CONTEXT_KEY, {
  state,
  setCurrentDate,
  setSelectedDate,
  setViewMode,
  next,
  prev,
});
</script>

<template>
  <div class="simple-calendar-root w-full h-full flex flex-col">
    <slot />
  </div>
</template>
