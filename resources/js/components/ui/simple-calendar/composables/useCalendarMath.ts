import {
  startOfMonth,
  endOfMonth,
  startOfWeek,
  endOfWeek,
  eachDayOfInterval,
  isSameDay,
  isSameMonth,
  addMonths,
  subMonths,
  addDays,
  subDays,
  format
} from 'date-fns';
import { CalendarDay, CalendarEvent, CalendarConfig } from '../types';

/**
 * Calendar Math Composable
 * 
 * Provides pure utility functions for date calculations and grid generation.
 */
export function useCalendarMath() {
  /**
   * Generates a 6x7 grid (42 days) for a given month to ensure consistency across UI.
   */
  const getDaysInMonthGrid = (
    baseDate: Date,
    selectedDate: Date | null,
    events: CalendarEvent[],
    config: CalendarConfig
  ): CalendarDay[] => {
    const monthStart = startOfMonth(baseDate);
    const monthEnd = endOfMonth(monthStart);
    
    // Start of the grid (might be in the previous month)
    const gridStart = startOfWeek(monthStart, { weekStartsOn: config.weekStartsOn });
    // End of the grid (ensuring we always have 42 days for a stable UI height)
    const gridEnd = addDays(gridStart, 41);

    const allDays = eachDayOfInterval({ start: gridStart, end: gridEnd });

    return allDays.map((date) => {
      const dayEvents = events.filter((event) => isSameDay(event.start, date));
      
      return {
        date,
        isCurrentMonth: isSameMonth(date, monthStart),
        isToday: isSameDay(date, new Date()),
        isSelected: selectedDate ? isSameDay(date, selectedDate) : false,
        events: dayEvents,
      };
    });
  };

  /**
   * Helper to format labels for the calendar header.
   */
  const getPeriodLabel = (date: Date, viewMode: 'month' | 'day', locale?: any): string => {
    if (viewMode === 'month') {
      return format(date, 'MMMM yyyy', { locale });
    }
    return format(date, 'PPPP', { locale });
  };

  return {
    getDaysInMonthGrid,
    getPeriodLabel,
    isSameDay,
    addMonths,
    subMonths,
    addDays,
    subDays,
  };
}
