<?php

namespace App\Http\Requests;

use App\Enums\AppointmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'start_time' => ['required', 'date'],
            'status' => ['required', Rule::enum(AppointmentStatus::class)],
            'total_price' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'price_items' => ['nullable', 'array'],
            'price_items.*' => ['exists:price_items,id'],
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'client_id.required' => 'Wybór klientki jest wymagany.',
            'client_id.exists' => 'Wybrana klientka nie istnieje w bazie.',
            'start_time.required' => 'Data i godzina wizyty są wymagane.',
            'start_time.date' => 'Pole musi zawierać poprawną datę.',
            'status.required' => 'Status wizyty jest wymagany.',
            'status.enum' => 'Wybrany status jest nieprawidłowy.',
            'total_price.required' => 'Cena jest wymagana.',
            'total_price.numeric' => 'Cena musi być liczbą.',
            'total_price.min' => 'Cena nie może być ujemna.',
            'notes.string' => 'Notatki muszą być tekstem.',
            'price_items.array' => 'Usługi muszą być przesłane jako lista.',
            'price_items.*.exists' => 'Jedna z wybranych usług nie istnieje w cenniku.',
        ];
    }
}
