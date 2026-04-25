/**
 * SimpleCalendar - Headless Calendar System
 * 
 * Public API for the SimpleCalendar module.
 */

// Core Components
export { default as SimpleCalendarRoot } from './parts/SimpleCalendarRoot.vue';
export { default as SimpleCalendarHeader } from './parts/SimpleCalendarHeader.vue';
export { default as SimpleCalendarGrid } from './parts/SimpleCalendarGrid.vue';

// Context & Types
export * from './types';
export { CALENDAR_CONTEXT_KEY } from './context';

// Composables (optional public use)
export { useCalendarMath } from './composables/useCalendarMath';
export { useCalendarState } from './composables/useCalendarState';
