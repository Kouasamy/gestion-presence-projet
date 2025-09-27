<?php

namespace App\Http\Requests\Presence;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePresenceRequest extends FormRequest
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
            'presences' => 'required|array',
            'presences.*.statut_presence_id' => 'required|exists:statut_presences,id',
            'presences.*.justification' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'presences.required' => 'Les présences sont obligatoires.',
            'presences.array' => 'Les présences doivent être un tableau.',
            'presences.*.statut_presence_id.required' => 'Le statut de présence est obligatoire.',
            'presences.*.statut_presence_id.exists' => 'Le statut de présence sélectionné n\'existe pas.',
            'presences.*.justification.string' => 'La justification doit être une chaîne de caractères.',
            'presences.*.justification.max' => 'La justification ne doit pas dépasser 255 caractères.',
        ];
    }
}
