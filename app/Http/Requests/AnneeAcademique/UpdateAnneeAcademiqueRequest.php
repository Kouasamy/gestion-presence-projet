<?php

namespace App\Http\Requests\AnneeAcademique;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateAnneeAcademiqueRequest extends FormRequest
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
        $anneeId = $this->route('annee');

        return [
            'annee' => [
                'required',
                'string',
                Rule::unique('annees_academiques')->ignore($anneeId),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'annee.required' => 'L\'année académique est obligatoire.',
            'annee.string' => 'L\'année académique doit être une chaîne de caractères.',
            'annee.unique' => 'Cette année académique existe déjà.',
        ];
    }
}
