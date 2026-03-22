<?php

namespace App\Http\Requests;

use App\Enums\PriceItemCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PriceItemRequest extends FormRequest
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
            'category' => ['required', Rule::enum(PriceItemCategory::class)],
            'price_min' => ['required', 'numeric', 'min:0'],
            'price_max' => ['nullable', 'numeric', 'min:0', 'gte:price_min'],
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
            'name.required' => 'Pole nazwa jest wymagane.',
            'category.required' => 'Pole kategoria jest wymagane.',
            'category.Illuminate\Validation\Rules\Enum' => 'Wybrana kategoria jest nieprawidłowa.',
            'price_min.required' => 'Pole cena minimalna jest wymagane.',
            'price_min.numeric' => 'Cena minimalna musi być liczbą.',
            'price_min.min' => 'Cena minimalna nie może być ujemna.',
            'price_max.numeric' => 'Cena maksymalna musi być liczbą.',
            'price_max.min' => 'Cena maksymalna nie może być ujemna.',
            'price_max.gte' => 'Cena maksymalna musi być większa lub równa cenie minimalnej.',
        ];
    }
}
