<?php

namespace App\Http\Requests\Seance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateSeanceRequest extends FormRequest
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
            'matiere_id' => 'required|exists:matieres,id',
            'enseignant_id' => 'required|exists:enseignants,id',
            'type_cours_id' => 'required|exists:type_cours,id',
            'statut_seance_id' => 'required|exists:statut_seances,id',
            'date_seance' => 'required|date',
            'heure_debut' => 'required',
            'heure_fin' => 'required|after:heure_debut',
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
            'matiere_id.required' => 'La matière est obligatoire.',
            'matiere_id.exists' => 'La matière sélectionnée n\'existe pas.',
            'enseignant_id.required' => 'L\'enseignant est obligatoire.',
            'enseignant_id.exists' => 'L\'enseignant sélectionné n\'existe pas.',
            'type_cours_id.required' => 'Le type de cours est obligatoire.',
            'type_cours_id.exists' => 'Le type de cours sélectionné n\'existe pas.',
            'statut_seance_id.required' => 'Le statut de la séance est obligatoire.',
            'statut_seance_id.exists' => 'Le statut de séance sélectionné n\'existe pas.',
            'date_seance.required' => 'La date de la séance est obligatoire.',
            'date_seance.date' => 'La date de la séance doit être une date valide.',
            'heure_debut.required' => 'L\'heure de début est obligatoire.',
            'heure_fin.required' => 'L\'heure de fin est obligatoire.',
            'heure_fin.after' => 'L\'heure de fin doit être postérieure à l\'heure de début.',
        ];
    }
}
