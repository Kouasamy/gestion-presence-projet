<?php

namespace App\Http\Requests\TypeCours;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreTypeCoursRequest extends FormRequest
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
            'nom_type_cours' => 'required|string|max:255|unique:type_cours,nom_type_cours',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nom_type_cours.required' => 'Le nom du type de cours est obligatoire.',
            'nom_type_cours.string' => 'Le nom du type de cours doit être une chaîne de caractères.',
            'nom_type_cours.max' => 'Le nom du type de cours ne doit pas dépasser 255 caractères.',
            'nom_type_cours.unique' => 'Ce nom de type de cours existe déjà.',
        ];
    }
}
