import { reactive, watch, toRaw } from 'vue';
import { CalendarState, CalendarViewMode, CalendarEvent, CalendarConfig } from '../types';
import { useCalendarMath } from './useCalendarMath';

/**
 * Calendar State Composable
 * 
 * Manages the reactive state and transitions for the calendar module.
 */
export function useCalendarState(initialData: {
  selectedDate: Date | null;
  events: CalendarEvent[];
  viewMode?: CalendarViewMode;
  config?: Partial<CalendarConfig>;
}) {
  const { addMonths, subMonths, addDays, subDays } = useCalendarMath();

  const state = reactive<CalendarState>({
    viewMode: initialData.viewMode || 'month',
    currentDate: initialData.selectedDate || new Date(),
    selectedDate: initialData.selectedDate,
    events: initialData.events,
    config: {
      weekStartsOn: initialData.config?.weekStartsOn ?? 1, // Default to Monday
      locale: initialData.config?.locale,
    },
  });

  const setCurrentDate = (date: Date) => {
    state.currentDate = date;
  };

  const setSelectedDate = (date: Date) => {
    state.selectedDate = date;
    state.currentDate = date;
  };

  const setViewMode = (mode: CalendarViewMode) => {
    state.viewMode = mode;
  };

  const next = () => {
    if (state.viewMode === 'month') {
      state.currentDate = addMonths(state.currentDate, 1);
    } else {
      state.currentDate = addDays(state.currentDate, 1);
    }
  };

  const prev = () => {
    if (state.viewMode === 'month') {
      state.currentDate = subMonths(state.currentDate, 1);
    } else {
      state.currentDate = subDays(state.currentDate, 1);
    }
  };

  return {
    state,
    setCurrentDate,
    setSelectedDate,
    setViewMode,
    next,
    prev,
  };
}
