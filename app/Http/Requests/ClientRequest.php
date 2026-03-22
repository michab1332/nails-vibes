<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'ig_name' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'note' => ['nullable', 'string'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Pole imię i nazwisko jest wymagane.',
            'name.string' => 'Imię i nazwisko musi być tekstem.',
            'name.max' => 'Imię i nazwisko nie może przekraczać 255 znaków.',
            'ig_name.string' => 'Nazwa Instagram musi być tekstem.',
            'ig_name.max' => 'Nazwa Instagram nie może przekraczać 255 znaków.',
            'phone_number.string' => 'Numer telefonu musi być tekstem.',
            'phone_number.max' => 'Numer telefonu nie może przekraczać 20 znaków.',
            'note.string' => 'Notatka musi być tekstem.',
        ];
    }
}
