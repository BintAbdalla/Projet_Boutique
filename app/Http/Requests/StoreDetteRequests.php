<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDetteRequests extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'clientId' => ['required', 'exists:clients,id'],
            'montantVerser' => ['nullable', 'integer', 'min:0'],
            'articles' => ['required', 'array', 'min:1'],
            'articles.*.articleId' => ['required', 'exists:articles,id'],
            'articles.*.quantiteVente' => ['required', 'integer', 'min:1'],
            'articles.*.prixVente' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Get the validation error messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'clientId.required' => 'Le client est obligatoire.',
            'clientId.exists' => 'Le client sélectionné n\'existe pas.',

            'montantVerser.integer' => 'Le montant versé doit être un entier.',
            'montantVerser.min' => 'Le montant versé ne peut pas être inférieur à zéro.',

            'articles.required' => 'Vous devez sélectionner au moins un article.',
            'articles.array' => 'La liste des articles doit être un tableau.',
            'articles.min' => 'Vous devez sélectionner au moins un article.',

            'articles.*.articleId.required' => 'L\'ID de l\'article est obligatoire.',
            'articles.*.articleId.exists' => 'L\'article sélectionné n\'existe pas.',

            'articles.*.quantiteVente.required' => 'La quantité de vente est obligatoire pour chaque article.',
            'articles.*.quantiteVente.integer' => 'La quantité de vente doit être un entier positif.',
            'articles.*.quantiteVente.min' => 'La quantité de vente doit être au moins 1.',

            'articles.*.prixVente.required' => 'Le prix de vente est obligatoire pour chaque article.',
            'articles.*.prixVente.integer' => 'Le prix de vente doit être un entier positif.',
            'articles.*.prixVente.min' => 'Le prix de vente doit être au moins 1.',
        ];
    }
}