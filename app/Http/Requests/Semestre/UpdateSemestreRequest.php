<?php

namespace App\Http\Requests\Semestre;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateSemestreRequest extends FormRequest
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
            'nom' => 'required|string',
            'date_debut_semestre' => 'required|date',
            'date_fin_semestre' => 'required|date|after_or_equal:date_debut_semestre',
            'annees_academiques_id' => 'required|exists:annees_academiques,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom du semestre est obligatoire.',
            'nom.string' => 'Le nom du semestre doit être une chaîne de caractères.',
            'date_debut_semestre.required' => 'La date de début du semestre est obligatoire.',
            'date_debut_semestre.date' => 'La date de début du semestre doit être une date valide.',
            'date_fin_semestre.required' => 'La date de fin du semestre est obligatoire.',
            'date_fin_semestre.date' => 'La date de fin du semestre doit être une date valide.',
            'date_fin_semestre.after_or_equal' => 'La date de fin du semestre doit être postérieure ou égale à la date de début.',
            'annees_academiques_id.required' => 'L\'année académique est obligatoire.',
            'annees_academiques_id.exists' => 'L\'année académique sélectionnée n\'existe pas.',
        ];
    }
}
