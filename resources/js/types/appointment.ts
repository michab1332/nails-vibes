import type { Client } from './client';
import type { PriceItem } from './price-item';

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
    created_at: string;
    updated_at: string;
}
