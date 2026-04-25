# SimpleCalendar - Headless Calendar System

SimpleCalendar is a modular, high-performance, and fully customizable calendar system built with **Vue 3**, **TypeScript**, and **date-fns**. It follows the **Headless UI** pattern, providing all the logic and state management while leaving the visual representation entirely to you.

## Features

- 🧩 **Headless Architecture**: Separates logic from UI using the Compound Components pattern.
- 📱 **Mobile First**: Built-in support for native-feeling interactions (Long Press with visual feedback).
- ⚡ **Performance**: Efficient grid generation (always 42 days for UI stability) and support for Lazy Loading.
- 🛡️ **Strict TypeScript**: Generic event types (`CalendarEvent<T>`) for full type-safety of your domain data.
- 🌍 **L10n Support**: Fully compatible with `date-fns` locales.
- 📐 **Multiple Views**: Supports Monthly Grid and Hourly Timeline (Day) views.

---

## Installation & Setup

The calendar lives in `resources/js/components/ui/simple-calendar/`. To use it, simply import the core components:

```typescript
import { 
  SimpleCalendarRoot, 
  SimpleCalendarHeader, 
  SimpleCalendarGrid 
} from '@/components/ui/simple-calendar';
```

---

## Core Components

### 1. `SimpleCalendarRoot`
The main orchestrator. It initializes the reactive state and provides it to all children via the `CALENDAR_CONTEXT`.

**Props:**
| Prop | Type | Default | Description |
| :--- | :--- | :--- | :--- |
| `modelValue` | `Date \| null` | `null` | The currently selected date (supports v-model). |
| `events` | `CalendarEvent<T>[]` | `[]` | Array of events to display. |
| `viewMode` | `'month' \| 'day'` | `'month'` | Active view mode. |
| `config` | `CalendarConfig` | `{ weekStartsOn: 1 }` | Global settings (locale, week start). |

**Emits:**
- `@update:modelValue`: Triggered on date selection.
- `@update:viewMode`: Triggered when changing views.
- `@dateSelect`: Triggered when a day is clicked.
- `@dateHold`: Triggered on long press (hold) on a day cell.

### 2. `SimpleCalendarHeader`
A headless wrapper for navigation. Use its scoped slot to render your own buttons and labels.

**Slot Props:**
| Prop | Type | Description |
| :--- | :--- | :--- |
| `label` | `string` | Formatted period string (e.g., "May 2026"). |
| `next` | `() => void` | Move to next month/day. |
| `prev` | `() => void` | Move to previous month/day. |
| `today` | `() => void` | Snap to today's date. |
| `currentView` | `string` | Current view mode. |

### 3. `SimpleCalendarGrid`
The dynamic view router. It automatically renders `MonthView` or `DayView` based on the state.

**Slots:**
- `#day`: Customize the cell of a single day (Month View).
- `#hour`: Customize the hour row (Day View).
- `#event`: Customize how an individual event is rendered.

---

## Data Structures

### `CalendarEvent<T>`
```typescript
export interface CalendarEvent<T = any> {
  id: string | number;
  start: Date;
  end: Date;
  data: T; // Your custom payload (e.g., Appointment model)
}
```

### `CalendarDay`
Passed to the `#day` slot.
```typescript
export interface CalendarDay {
  date: Date;
  isCurrentMonth: boolean;
  isToday: boolean;
  isSelected: boolean;
  events: CalendarEvent[];
}
```

---

## Advanced Usage

### Custom Day Cell with Event Dots
```vue
<SimpleCalendarGrid>
  <template #day="{ date, events, isSelected, isToday }">
    <div class="my-custom-cell">
      <span>{{ format(date, 'd') }}</span>
      <div class="dots-container">
        <div v-for="e in events" :class="e.data.statusColor" />
      </div>
    </div>
  </template>
</SimpleCalendarGrid>
```

### Handling Long Press (Hold)
```vue
<SimpleCalendarRoot @date-hold="(date) => openCreateModal(date)">
  ...
</SimpleCalendarRoot>
```

---

## Internals & Logic

### `useCalendarState`
Manages the reactive source of truth. It handles transitions between months and keeps track of the "logical" date vs the "selected" date.

### `useCalendarMath`
A stateless utility composable that performs the heavy lifting for date grid generation using `date-fns`. It ensures that every month view always displays 6 weeks (42 days) to prevent layout shifts when navigating between months of different lengths.
