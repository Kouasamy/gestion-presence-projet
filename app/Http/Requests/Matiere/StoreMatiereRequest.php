<?php

namespace App\Http\Requests\Matiere;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreMatiereRequest extends FormRequest
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
            'nom_matiere' => 'required|string|max:255|unique:matieres,nom_matiere',
            'description' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nom_matiere.required' => 'Le nom de la matière est obligatoire.',
            'nom_matiere.string' => 'Le nom de la matière doit être une chaîne de caractères.',
            'nom_matiere.max' => 'Le nom de la matière ne doit pas dépasser 255 caractères.',
            'nom_matiere.unique' => 'Ce nom de matière existe déjà.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'description.max' => 'La description ne doit pas dépasser 1000 caractères.',
        ];
    }
}
