<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
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
            'products' => 'required|array',
            'products.*' => 'required|exists:products,id',
            'method' => 'required|in:cash,card,mix',
            'cash' => 'required_if:method,cash|required_if:method,mix|numeric',
            'card' => 'required_if:method,card|required_if:method,mix|numeric',
        ];
    }

    public function messages()
    {
        return [
            'products.required' => 'El producto es requerido',
            'products.*.required' => 'El producto es requerido',
            'products.*.exists' => 'El producto no existe',
            'method.required' => 'El metodo es requerido',
            'method.in' => 'El metodo no es valido',
            'cash.required_if' => 'El efectivo es requerido',
            'cash.numeric' => 'El efectivo debe ser un numero',
            'card.required_if' => 'La tarjeta es requerida',
            'card.numeric' => 'La tarjeta debe ser un numero',
        ];
    }
}
