<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaiementRequests extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette demande.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtenez les règles de validation qui s'appliquent à la demande.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'montant' => [
                'required',
                'numeric',
                'gt:0', // Le montant doit être supérieur à zéro
                function ($attribute, $value, $fail) {
                    // Obtenez l'ID de la dette à partir de la route
                    $detteId = $this->route('id');
                    
                    // Trouvez la dette correspondante
                    $dette = \App\Models\Dettes::find($detteId);

                    if ($dette && $value > $dette->montantRestant) {
                        // Si le montant est supérieur au montant restant
                        $fail('Le montant ne peut pas être supérieur au montant restant.');
                    }
                }
            ],
        ];
    }

    /**
     * Obtenez les messages d'erreur de validation.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'montant.required' => 'Le montant est requis.',
            'montant.numeric' => 'Le montant doit être un nombre.',
            'montant.gt' => 'Le montant doit être supérieur à zéro.',
            'montant.max' => 'Le montant ne peut pas dépasser le montant restant.',
        ];
    }
}
