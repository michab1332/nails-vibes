import { Client } from './client'; // Assuming client types exist
import { PriceItem } from './price-item'; // Assuming price-item types exist

export enum AppointmentStatus {
  Scheduled = 'Zaplanowana',
  Completed = 'Zakończona',
  Cancelled = 'Anulowana',
  NoShow = 'Nieobecność'
}

export interface Appointment {
  id: number;
  client_id: number;
  client: {
    id: number;
    name: string;
    phone_number: string;
  };
  price_items: {
    id: number;
    name: string;
    price_min: string | number;
    price_max: string | number;
  }[];
  start_time: string; // ISO string
  status: AppointmentStatus;
  total_price: number | string | null;
  notes: string | null;
}
