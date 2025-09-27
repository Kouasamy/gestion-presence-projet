<?php

namespace App\Http\Requests\Presence;

use App\Models\Seance;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EnregistrerPresenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $seanceId = $this->route('seanceId');
        $enseignantId = Auth::user()->enseignant->id ?? null;

        if (! $enseignantId) {
            return false;
        }

        $seance = Seance::where('id', $seanceId)
            ->where('enseignant_id', $enseignantId)
            ->first();

        if (! $seance) {
            return false;
        }

        // Vérifier si la séance est de type "Cours" (présentiel)
        $typeCours = strtolower($seance->typeCours->nom_type_cours);
        if ($typeCours !== 'cours') {
            return false;
        }

        // Vérifier si la modification est autorisée (dans les 14 jours après la séance)
        $now = Carbon::now();
        $seanceDate = Carbon::parse($seance->date_seance);

        if ($now->diffInDays($seanceDate) > 14 && $now->greaterThan($seanceDate)) {
            return false;
        }

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
            'presences' => 'required|array',
            'presences.*.statut_presence_id' => 'required|exists:statut_presences,id',
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
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'presences' => 'présences',
            'presences.*.statut_presence_id' => 'statut de présence',
        ];
    }
}
