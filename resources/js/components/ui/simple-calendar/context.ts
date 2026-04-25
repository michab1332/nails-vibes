import { InjectionKey, Ref } from 'vue';
import { CalendarState, CalendarViewMode } from './types';

/**
 * Injection Keys for Provide/Inject pattern.
 * These are used to share the calendar state and actions with child components.
 */

export interface CalendarContext {
  state: CalendarState;
  /** Set the currently viewed date (month/day) */
  setCurrentDate: (date: Date) => void;
  /** Select a specific date */
  setSelectedDate: (date: Date) => void;
  /** Triggered when a date is held (long press) */
  onDateHold: (date: Date) => void;
  /** Switch between view modes (month, day) */
  setViewMode: (mode: CalendarViewMode) => void;
  /** Move to the next period (month or day) */
  next: () => void;
  /** Move to the previous period (month or day) */
  prev: () => void;
}

export const CALENDAR_CONTEXT_KEY = Symbol() as InjectionKey<CalendarContext>;
