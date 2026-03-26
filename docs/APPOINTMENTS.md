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
│       └── AppointmentStatusBadge.vue# Komponent odznaki statusu (używa shadcn Badge)
└── pages/
    └── Appointments/                 # Nowy folder stron
        ├── Index.vue                 # Główny widok listy wizyt (kafelki, grupowanie po dacie)
        ├── Create.vue                # Widok dodawania
        └── Edit.vue                  # Widok edycji
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
```php
namespace App\Enums;

enum AppointmentStatus: string
{
    case SCHEDULED = 'Zaplanowana';
    case COMPLETED = 'Zakończona';
    case CANCELLED = 'Anulowana';
    case NO_SHOW = 'Nieobecność';

    public static function labels(): array { /* ... */ }
    public function color(): string { /* Zwraca wariant koloru dla frontendu np. 'default', 'success', 'destructive', 'secondary' wg nazw z shadcn */ }
}
```

### 3.2 `App\Models\Appointment`
*   **Fillable**: `['client_id', 'start_time', 'status', 'total_price', 'notes']`
*   **Casts**:
    *   `start_time` => `'datetime'`
    *   `status` => `AppointmentStatus::class`
*   **Relacje**:
    *   `client()`: `return $this->belongsTo(Client::class);`
    *   `priceItems()`: `return $this->belongsToMany(PriceItem::class);`

### 3.3 `App\Http\Requests\AppointmentRequest`
*   `client_id`: `['required', 'exists:clients,id']`
*   `start_time`: `['required', 'date']` (Można dodać `after_or_equal:now` dla tworzenia, ale przy edycji może wymagać ostrożności).
*   `status`: `['required', Rule::enum(AppointmentStatus::class)]`
*   `total_price`: `['required', 'numeric', 'min:0']`
*   `notes`: `['nullable', 'string']`
*   `price_items`: `['nullable', 'array']`
*   `price_items.*`: `['exists:price_items,id']`

### 3.4 `App\Services\AppointmentService`
Klasa rozszerzająca `BaseService`.
*   `__construct(Appointment $model)`
*   `index()`: Zwraca wizyty posortowane po `start_time` rosnąco, używa `with(['client', 'priceItems'])`.
*   `store(array $data)`:
    *   Używa `DB::transaction`.
    *   Tworzy wizytę: `$appointment = $this->model->create($data)`.
    *   Jeśli istnieje tablica `$data['price_items']`, synchronizuje usługi: `$appointment->priceItems()->sync($data['price_items'])`.
    *   Zwraca `$appointment`.
*   `update(Appointment $appointment, array $data)`:
    *   Używa `DB::transaction`.
    *   Aktualizuje dane podstawowe.
    *   Synchronizuje usługi za pomocą `$appointment->priceItems()->sync(...)`.

### 3.5 `App\Http\Controllers\AppointmentController`
Standardowy kontroler Inertia.
*   Metody: `index`, `create`, `store`, `edit`, `update`, `destroy`.
*   Do widoków `create` i `edit` musi przekazywać listę klientek (`Client::select('id', 'name', 'phone_number')->get()`) oraz cennik (`PriceItem::select('id', 'name', 'price_min', 'price_max')->get()`), aby formularz mógł je wyświetlić.

---

## 4. Szczegóły Implementacji Frontendowej

### 4.1 Typy (TypeScript) - `resources/js/types/appointment.ts`
```typescript
import { Client, PriceItem } from './index'; // założenie istniejących typów

export enum AppointmentStatus {
    Scheduled = 'Zaplanowana',
    Completed = 'Zakończona',
    Cancelled = 'Anulowana',
    NoShow = 'Nieobecność'
}

export interface Appointment {
    id: number;
    client_id: number;
    client: Client;
    price_items: PriceItem[];
    start_time: string; // ISO string
    status: AppointmentStatus;
    total_price: number | string;
    notes: string | null;
}
```

### 4.2 Logika Ceny (Composable) - `resources/js/composables/useAppointmentPrice.ts`
Zadaniem tego hooka jest obsługa dynamicznego pola "Cena" na formularzu.
*   **Wejście**: Referencja do tablicy wybranych `PriceItem` oraz referencja do zadeklarowanej przez użytkownika ceny.
*   **Logika (`watch`)**: Gdy użytkownik zaznacza/odznacza usługi z cennika, system podlicza sumę ich `price_min`.
*   **Ochrona przed nadpisaniem (Dirty Flag)**: Jeśli użytkownik *ręcznie* kliknie w pole "Cena" i wpisze własną wartość (np. rabat), system podnosi flagę `isDirtyPrice = true` i przestaje automatycznie podliczać cenę przy zmianie usług (chyba że pole z ceną zostanie wyczyszczone).
*   **Eksport**: Zwraca wyliczoną sumę, metodę do ręcznego resetowania oraz aktualną kwotę.

### 4.3 Komponenty UI (`resources/js/components/appointments/`)

#### 1. `AppointmentStatusBadge.vue`
*   Używa `import { Badge } from '@/components/ui/badge'`.
*   Przyjmuje prop `status` typu `AppointmentStatus`.
*   Mapuje statusy na warianty kolorystyczne Badge'a (np. 'default', 'destructive', 'outline').

#### 2. `AppointmentCard.vue`
*   **Design**: Minimalistyczny kafelek. Przystosowany do widoku jednokulumnowego na mobile i wielokolumnowego na desktopie.
*   **Props**: `appointment: Appointment`.
*   **Układ wewnątrz kafelka (Flex/Grid)**:
    *   **Góra**: Wyraźna godzina (np. tekst pogrubiony, duży) obok daty.
    *   **Środek**: Nazwa klientki (kliknięcie może prowadzić do jej profilu), mała ikona telefonu. Poniżej mniejszym tekstem złączone nazwy usług (np. "Hybryda + Zdobienie").
    *   **Dół**: Odznaka statusu (`AppointmentStatusBadge`) oraz kwota po prawej stronie wyjustowana.
*   **Akcje**: Przycisk (ikona 'MoreHorizontal' z lucide-vue-next) otwierający DropdownMenu z akcjami: Edytuj, Usuń, Zmień Status.

#### 3. `AppointmentForm.vue`
*   Współdzielony komponent do dodawania i edycji.
*   **Prop**: `form` (obiekt z Inertia useForm), `clients` (lista klientek), `priceItems` (lista usług).
*   **Pola**:
    *   **Klientka**: Użycie komponentu wyboru (Combobox/Select z wyszukiwaniem).
    *   **Data i Godzina**: Systemowy `<input type="datetime-local">` (z odpowiednim stylowaniem shadcn) lub gotowy DateTimePicker.
    *   **Usługi**: Multi-select lub lista checkboxów z nazwami i cenami minimalnymi.
    *   **Cena**: Pole liczbowe (nadpisywane przez `useAppointmentPrice`, chyba że wpisane ręcznie).
    *   **Status**: Select dla `AppointmentStatus` (przy edycji).
    *   **Notatka**: `<Textarea>`.

### 4.4 Strony (Views) (`resources/js/pages/Appointments/`)

#### `Index.vue`
*   Główny widok kalendarza/listy.
*   **Logika Grupowania**: Dane z backendu to płaska lista wizyt. Komponent pobiera tę listę i w `computed()` property (np. `groupedAppointments`) grupuje je w obiekt/Mapę kluczowaną datą ("Dzisiaj", "Jutro", lub "25 Marca 2026").
*   **Wyświetlanie**:
    *   Pętla `v-for` po pogrupowanych datach, tworząca sekcje z nagłówkiem (Data).
    *   Wewnątrz sekcji grid (`<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">`) renderujący `AppointmentCard`.
*   **Usuwanie (Modal)**: Zgodnie z wytycznymi z innych widoków (np. `Inspirations/Index.vue`), należy użyć komponentu `Dialog` z `shadcn-vue` jako potwierdzenia usunięcia kafelka, zachowując ten sam styl "wyskakującego okienka" z overlayem i blur.

---

## 5. Wytyczne Kodowania i Stylu (Czysty Kod)
1.  **Strict TypeScript**: Zawsze typuj właściwości, propsy, zdarzenia emitowane (emits) i reaktywne zmienne. Unikaj `any`.
2.  **Kapsułkowanie (Encapsulation)**: Logika API, nawigacji Inertia i modale powinny żyć w "Stronach" (`Pages`), podczas gdy "Komponenty" (`Components`) powinny być jak najbardziej "głupie" (Dumb Components) i polegać na wstrzykiwanych Propsach i Emitowanych zdarzeniach.
3.  **Spójność z Shadcn**: Korzystaj wyłącznie z zainstalowanych komponentów w `resources/js/components/ui/` do budowy formularzy, kart, odznak, przycisków i okien dialogowych.
4.  **Mobile First**: Wszystkie klasy Tailwind (marginesy, paddingi, gap) zawsze układaj z myślą o telefonach (np. `p-4`), używając breakpointów dla większych ekranów (np. `sm:p-6`).

---
Z dokumentacją gotową, implementację należy zacząć od **Bazy Danych (Migracje)**, przejść przez **Backend Core (Modele, Enums, Serwisy)**, a na końcu zbudować **Frontend (Typy, UI, Views)**.
