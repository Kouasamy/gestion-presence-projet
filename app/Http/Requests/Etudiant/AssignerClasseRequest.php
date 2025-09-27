<?php

namespace App\Http\Requests\Etudiant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AssignerClasseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role->nom_role === 'coordinateur';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'classe_id' => 'required|exists:classes,id',
            'annee_academique_id' => 'required|exists:annees_academiques,id',
            'date_debut' => 'required|date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'classe_id.required' => 'La classe est obligatoire.',
            'classe_id.exists' => 'La classe sélectionnée n\'existe pas.',
            'annee_academique_id.required' => 'L\'année académique est obligatoire.',
            'annee_academique_id.exists' => 'L\'année académique sélectionnée n\'existe pas.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_debut.date' => 'La date de début doit être une date valide.',
        ];
    }
}
