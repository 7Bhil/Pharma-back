<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;

class CreatePharmacieRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'error' => true,
            'message' => 'Erreur de validation',
            'errors' => $validator->errors(),
            'errorlist' => $validator->errors(),
        ], 422));
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'=> ['required', 'string', 'max:255',  'regex:/^[\pL\s\-\']+$/u'],
            'prenom'=> ['required', 'string', 'max:255',  'regex:/^[\pL\s\-\']+$/u'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password'=> ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'telephone'=> ['nullable', 'string', 'max:15', 'regex:/^01[4569][0-9]{7}$/'],
            'nom_pharmacie'=> ['required', 'string', 'max:255', 'regex:/^[\pL\s\-\']+$/u'],
            'numero_pharmacie'=> ['required', 'string', 'max:50', 'unique:pharmacies,numero_pharmacie', 'regex:/^01[4569][0-9]{7}$/'],
            'ville'=> ['required', 'string', 'max:100', 'regex:/^[\pL\s\-\']+$/u'],
            'quatier'=> ['required', 'string', 'max:100', 'regex:/^[\pL\s\-\']+$/u'],
            'latitude'=> ['required', 'numeric', 'between:-90,90'],
            'longitude'=> ['required', 'numeric', 'between:-180,180'],
        ];
    }
    public function messages(): array
    {
        return [
            'name.required'  => 'Le nom est obligatoire.',
            'name.string'    => 'Le nom doit être une chaîne de caractères.',
            'name.max'       => 'Le nom ne peut pas dépasser 255 caractères.',
            'name.regex'     => 'Le nom ne peut contenir que des lettres, espaces, tirets et apostrophes.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'prenom.string'   => 'Le prénom doit être une chaîne de caractères.',
            'prenom.max'      => 'Le prénom ne peut pas dépasser 255 caractères.',
            'prenom.regex'    => 'Le prénom ne peut contenir que des lettres, espaces, tirets et apostrophes.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email'    => 'L\'adresse email n\'est pas valide.',
            'email.unique'   => 'Cette adresse email est déjà utilisée.',
            'password.required'  => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'telephone.string' => 'Le numéro de téléphone doit être une chaîne de caractères.',
            'telephone.max'    => 'Le numéro de téléphone ne peut pas dépasser 15 caractères.',
            'telephone.regex'  => 'Le numéro de téléphone n\'est pas valide (format attendu : 01XXXXXXXX).',
            'nom_pharmacie.required' => 'Le nom de la pharmacie est obligatoire.',
            'nom_pharmacie.string'   => 'Le nom de la pharmacie doit être une chaîne de caractères.',
            'nom_pharmacie.max'      => 'Le nom de la pharmacie ne peut pas dépasser 255 caractères.',
            'nom_pharmacie.regex'    => 'Le nom de la pharmacie ne peut contenir que des lettres, espaces, tirets et apostrophes.',
            'numero_pharmacie.required' => 'Le numéro de la pharmacie est obligatoire.',
            'numero_pharmacie.string'   => 'Le numéro de la pharmacie doit être une chaîne de caractères.',
            'numero_pharmacie.max'      => 'Le numéro de la pharmacie ne peut pas dépasser 50 caractères.',
            'numero_pharmacie.unique'   => 'Ce numéro de pharmacie est déjà enregistré.',
            'numero_pharmacie.regex'    => 'Le numéro de la pharmacie n\'est pas valide (format attendu : 01XXXXXXXX).',
            'ville.required' => 'La ville est obligatoire.',
            'ville.string'   => 'La ville doit être une chaîne de caractères.',
            'ville.max'      => 'La ville ne peut pas dépasser 100 caractères.',
            'ville.regex'    => 'La ville ne peut contenir que des lettres, espaces, tirets et apostrophes.',
            'quatier.required' => 'Le quartier est obligatoire.',
            'quatier.string'   => 'Le quartier doit être une chaîne de caractères.',
            'quatier.max'      => 'Le quartier ne peut pas dépasser 100 caractères.',
            'quatier.regex'    => 'Le quartier ne peut contenir que des lettres, espaces, tirets et apostrophes.',
            'latitude.required' => 'La latitude est obligatoire.',
            'latitude.numeric'  => 'La latitude doit être un nombre.',
            'latitude.between'  => 'La latitude doit être comprise entre -90 et 90.',
            'longitude.required' => 'La longitude est obligatoire.',
            'longitude.numeric'  => 'La longitude doit être un nombre.',
            'longitude.between'  => 'La longitude doit être comprise entre -180 et 180.',
        ];
    }
}
