<?php

namespace App\Http\Requests\EmploiDuTemps;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreEmploiDuTempsRequest extends FormRequest
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
            'nb_semaines' => 'nullable|integer|min:1|max:52',
            'horaires' => 'nullable|array',
            'horaires.*.heure_debut' => 'nullable|date_format:H:i',
            'horaires.*.heure_fin' => 'nullable|date_format:H:i|after:horaires.*.heure_debut',
            'seances' => 'required|array',
            'seances.*.matiere_id' => 'required|exists:matieres,id',
            'seances.*.enseignant_id' => 'required|exists:enseignants,id',
            'seances.*.type_cours_id' => 'required|exists:type_cours,id',
            'seances.*.date_seance' => 'required|date',
            'seances.*.heure_debut' => 'required|date_format:H:i',
            'seances.*.heure_fin' => 'required|date_format:H:i|after:seances.*.heure_debut',
            'seances.*.periode' => 'required|in:matin,soir',
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
            'nb_semaines.integer' => 'Le nombre de semaines doit être un nombre entier.',
            'nb_semaines.min' => 'Le nombre de semaines doit être au moins 1.',
            'nb_semaines.max' => 'Le nombre de semaines ne peut pas dépasser 52.',
            'horaires.array' => 'Les horaires doivent être un tableau.',
            'horaires.*.heure_debut.date_format' => 'L\'heure de début doit être au format HH:MM.',
            'horaires.*.heure_fin.date_format' => 'L\'heure de fin doit être au format HH:MM.',
            'horaires.*.heure_fin.after' => 'L\'heure de fin doit être après l\'heure de début.',
            'seances.required' => 'Les séances sont obligatoires.',
            'seances.array' => 'Les séances doivent être un tableau.',
            'seances.*.matiere_id.required' => 'La matière est obligatoire.',
            'seances.*.matiere_id.exists' => 'La matière sélectionnée n\'existe pas.',
            'seances.*.enseignant_id.required' => 'L\'enseignant est obligatoire.',
            'seances.*.enseignant_id.exists' => 'L\'enseignant sélectionné n\'existe pas.',
            'seances.*.type_cours_id.required' => 'Le type de cours est obligatoire.',
            'seances.*.type_cours_id.exists' => 'Le type de cours sélectionné n\'existe pas.',
            'seances.*.date_seance.required' => 'La date de la séance est obligatoire.',
            'seances.*.date_seance.date' => 'La date de la séance doit être une date valide.',
            'seances.*.heure_debut.required' => 'L\'heure de début est obligatoire.',
            'seances.*.heure_debut.date_format' => 'L\'heure de début doit être au format HH:MM.',
            'seances.*.heure_fin.required' => 'L\'heure de fin est obligatoire.',
            'seances.*.heure_fin.date_format' => 'L\'heure de fin doit être au format HH:MM.',
            'seances.*.heure_fin.after' => 'L\'heure de fin doit être après l\'heure de début.',
            'seances.*.periode.required' => 'La période est obligatoire.',
            'seances.*.periode.in' => 'La période doit être matin ou soir.',
        ];
    }
}
