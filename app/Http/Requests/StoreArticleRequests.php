<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Traits\Responsetrait;
use App\Enums\StateEnums;

class StoreArticleRequests extends FormRequest
{
    use Responsetrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Vous pouvez le régler sur `true` si vous souhaitez que tous les utilisateurs puissent faire cette demande
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'libelle' => ['required', 'string', 'max:255', 'unique:articles,libelle'],
            'prix' => ['required', 'numeric', 'min:0'],
            'qteStock' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array
     */
    public function messages()
    {
        $rules =  [
            'libelle.required' => 'Le libelle est obligatoire.',
            'libelle.unique' => 'Le libelle doit être unique.',
            'prix.required' => 'Le prix est obligatoire.',
            'prix.numeric' => 'Le prix doit être un nombre.',
            'prix.min' => 'Le prix doit être supérieur ou égal à 0.',
            'qteStock.required' => 'La quantité en stock est obligatoire.',
            'qteStock.integer' => 'La quantité en stock doit être un nombre entier.',
            'qteStock.min' => 'La quantité en stock doit être supérieure ou égale à 0.',
        ];
        return $rules;
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     * @throws HttpResponseException
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->sendResponse($validator->errors(), StateEnums::ECHEC, 422));
    }
}
