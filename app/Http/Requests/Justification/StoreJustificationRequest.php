<?php

namespace App\Http\Requests\Justification;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreJustificationRequest extends FormRequest
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
            'motif' => 'required|string|max:255',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'motif.required' => 'Le motif est obligatoire.',
            'motif.string' => 'Le motif doit être une chaîne de caractères.',
            'motif.max' => 'Le motif ne doit pas dépasser 255 caractères.',
            'document.file' => 'Le document doit être un fichier.',
            'document.mimes' => 'Le document doit être au format PDF, JPG, JPEG ou PNG.',
            'document.max' => 'Le document ne doit pas dépasser 2Mo.',
        ];
    }
}
