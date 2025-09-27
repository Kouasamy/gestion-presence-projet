<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreRoleRequest extends FormRequest
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
            'nom_role' => 'required|string|max:255|unique:roles,nom_role',
            'description' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nom_role.required' => 'Le nom du rôle est obligatoire.',
            'nom_role.string' => 'Le nom du rôle doit être une chaîne de caractères.',
            'nom_role.max' => 'Le nom du rôle ne doit pas dépasser 255 caractères.',
            'nom_role.unique' => 'Ce nom de rôle existe déjà.',
            'description.string' => 'La description doit être une chaîne de caractères.',
        ];
    }
}
