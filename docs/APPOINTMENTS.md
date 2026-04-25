# Blueprint Architektury: Moduł Zapisów (Appointments)

Ten dokument stanowi kompletną, wyczerpującą specyfikację techniczną dla modułu Zapisów (Appointments) w aplikacji Nails Planner. Służy jako "Source of Truth" (Główne Źródło Prawdy) dla każdego agenta AI (w tym Gemini) oraz programisty wdrażającego ten moduł.

---

## 1. Drzewo Katalogów i Plików do Utworzenia/Modyfikacji

### Backend (Laravel)
```text
app/
├── Enums/
│   └── AppointmentStatus.php         # Nowy Enum ze statusami wizyt
├── Http/
│   ├── Controllers/
│   │   └── AppointmentController.php # Nowy kontroler obsługujący żądania HTTP i Inertia
│   └── Requests/
│       └── AppointmentRequest.php    # Nowy FormRequest z logiką walidacji
├── Models/
│   └── Appointment.php               # Nowy Model Eloquent
└── Services/
    └── AppointmentService.php        # Nowy Service Layer dziedziczący po BaseService

database/
└── migrations/
    ├── 202X_XX_XX_XXXXXX_create_appointments_table.php
    └── 202X_XX_XX_XXXXXX_create_appointment_price_item_table.php

routes/
└── admin.php                         # Modyfikacja: dodanie resource route 'appointments'
```

### Frontend (Vue 3 + TypeScript + Inertia)
```text
resources/js/
├── types/
│   └── appointment.ts                # Nowe definicje typów TypeScript dla wizyt
├── composables/
│   └── useAppointmentPrice.ts        # Nowy composable do dynamicznego obliczania ceny
├── components/
│   └── appointments/                 # Nowy folder na komponenty domenowe
│       ├── AppointmentCard.vue       # Komponent kafelka wizyty (Mobile-first UI)
│       ├── AppointmentForm.vue       # Formularz dodawania/edycji wizyty
│       ├── AppointmentFormModal.vue  # Wrapper formularza w oknie dialogowym
│       └── AppointmentStatusBadge.vue# Komponent odznaki statusu (używa shadcn Badge)
├── components/ui/simple-calendar/    # Headlessowa biblioteka kalendarza
└── pages/
    ├── Appointments/                 # Widok listy wizyt
    │   ├── Index.vue
    │   ├── Create.vue
    │   └── Edit.vue
    └── AppointmentsCalendar/         # Widok interaktywnego terminarza
        └── Index.vue
```

---

## 2. Architektura Bazy Danych

### Migracja 1: `appointments`
Tabela przechowująca główne dane wizyty. Zależy od tabeli `clients`.
*   `id`: `id()`
*   `client_id`: `foreignId('client_id')->constrained()->cascadeOnDelete()`
*   `start_time`: `dateTime('start_time')`
*   `status`: `string('status')->default('Zaplanowana')` (Wartość z Enuma)
*   `total_price`: `decimal('total_price', 10, 2)->nullable()` (Może być null, dopóki nie zostanie wyliczona, lub zapisywana na sztywno).
*   `notes`: `text('notes')->nullable()`
*   `timestamps()`

### Migracja 2: `appointment_price_item` (Pivot)
Tabela łącząca wiele usług (PriceItem) z jedną wizytą (Appointment). Pozwala to na przypisanie wielu usług do jednego spotkania.
*   `appointment_id`: `foreignId('appointment_id')->constrained()->cascadeOnDelete()`
*   `price_item_id`: `foreignId('price_item_id')->constrained()->cascadeOnDelete()`

---

## 3. Szczegóły Implementacji Backendowej

### 3.1 `App\Enums\AppointmentStatus`
Standardowy Enum obsługujący stany wizyty: `SCHEDULED`, `COMPLETED`, `CANCELLED`, `NO_SHOW`.

### 3.2 `App\Http\Controllers\AppointmentController`
Obsługuje inteligentne przekierowania (`smartRedirect`). Jeśli akcja (store/update) pochodzi z modala (np. na kalendarzu), system wykonuje `back()`, odświeżając dane bez opuszczania strony. Jeśli pochodzi z pełnej strony formularza, wraca do listy.

---

## 4. Widok Kalendarza (iOS Style)

Dla zapewnienia najlepszego UX na urządzeniach mobilnych, wdrożono dedykowany widok terminarza pod trasą `/admin/appointments/calendar`.

### 4.1 Implementacja "SimpleCalendar"
Kalendarz został zbudowany jako niezależna, otypowana w TypeScript biblioteka Headless UI.
*   **Headless architecture**: Cała logika dat (date-fns) i stanu (useCalendarState) jest oddzielona od warstwy prezentacji.
*   **Lazy Loading**: Przy zmianie miesiąca system dociąga z bazy danych tylko wizyty dla wybranego zakresu dat, co zapewnia wysoką wydajność.
*   **Mobile Interactivity**: Obsługa "Long Press" (przytrzymanie) z natywnym feedbackiem wizualnym (animacja skali).

### 4.2 Funkcje Interaktywne
*   **Kliknięcie w dzień**: Natychmiastowe filtrowanie listy wizyt pod kalendarzem (lokalne).
*   **Przytrzymanie dnia**: Automatyczne otwarcie modalu rezerwacji z predefiniowaną datą.
*   **Szybka edycja**: Możliwość edycji i usuwania wizyt bezpośrednio z listy pod kalendarzem za pomocą Modali (Dialog), bez przeładowania strony.

---

## 5. Wytyczne Kodowania i Stylu
1.  **Strict TypeScript**: Zakaz używania `any`. Wykorzystanie Generyków dla payloadów eventów.
2.  **Encapsulation**: Logika formularza zamknięta w `AppointmentForm.vue`, reużywalna w Modalu i na stronach.
3.  **Mobile First**: Optymalizacja dotyku, blokada systemowych menu przy długim naciśnięciu.
