<?php

namespace App\Http\Requests\StatutSeance;

use Illuminate\Foundation\Http\FormRequest;

class StoreStatutSeanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom_seance' => 'required|string|max:255|unique:statut_seances,nom_seance',
            'couleur' => 'required|string|max:7',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nom_seance.required' => 'Le nom de la séance est obligatoire.',
            'nom_seance.unique' => 'Ce nom de séance existe déjà.',
            'couleur.required' => 'La couleur est obligatoire.',
            'couleur.max' => 'La couleur ne doit pas dépasser 7 caractères.',
        ];
    }
}
