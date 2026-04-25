/**
 * SimpleCalendar Types
 * 
 * Strict type definitions for the headless calendar module.
 * All types are generic to allow custom data payloads.
 */

/**
 * Represents the view mode of the calendar.
 * 'month' - Standard monthly grid.
 * 'day' - Hourly timeline view.
 */
export type CalendarViewMode = 'month' | 'day';

/**
 * Generic interface for calendar events.
 * @template T - The type of the custom payload data.
 */
export interface CalendarEvent<T = any> {
  id: string | number;
  start: Date;
  end: Date;
  /** Custom data payload provided by the user */
  data: T;
}

/**
 * Metadata for a single day in the calendar grid.
 */
export interface CalendarDay {
  date: Date;
  /** Whether the day belongs to the currently viewed month */
  isCurrentMonth: boolean;
  /** Whether the day is today */
  isToday: boolean;
  /** Whether the day is currently selected by the user */
  isSelected: boolean;
  /** List of events occurring on this specific day */
  events: CalendarEvent[];
}

/**
 * Global configuration options for the calendar.
 */
export interface CalendarConfig {
  /** First day of the week (0 for Sunday, 1 for Monday) */
  weekStartsOn: 0 | 1 | 2 | 3 | 4 | 5 | 6;
  /** Locale object from date-fns */
  locale?: any;
}

/**
 * The internal state shared across all SimpleCalendar components.
 */
export interface CalendarState {
  /** Currently active view mode */
  viewMode: CalendarViewMode;
  /** The date used to determine which month/day to display */
  currentDate: Date;
  /** The date explicitly selected by the user */
  selectedDate: Date | null;
  /** Collection of all events to be displayed */
  events: CalendarEvent[];
  /** Configuration settings */
  config: CalendarConfig;
}
