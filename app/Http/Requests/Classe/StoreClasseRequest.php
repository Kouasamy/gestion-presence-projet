<?php

namespace App\Http\Requests\Classe;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreClasseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role->nom_role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom_classe' => 'required|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nom_classe.required' => 'Le nom de la classe est obligatoire.',
            'nom_classe.string' => 'Le nom de la classe doit être une chaîne de caractères.',
            'nom_classe.max' => 'Le nom de la classe ne doit pas dépasser 255 caractères.',
        ];
    }
}
