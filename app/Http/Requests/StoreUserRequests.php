<?php
namespace App\Http\Requests;

use App\Enums\StateEnums;
use App\Enums\RoleEnums;
use App\Enums\EtatEnums;
use App\Rules\CustumPasswordRules;
use App\Traits\Responsetrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;



class StoreUserRequests extends FormRequest
{
    use Responsetrait;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'login' => 'required|string|max:255|unique:users,login',
            'role' => ['nullable', 'exists:roles,role'],
            'password' => ['required', 'confirmed', new CustumPasswordRules()],
            'etat' => ['required', 'in:' . implode(',', EtatEnums::values())],
            'client_id' => ['nullable', 'exists:clients,id'], // Assurez-vous que ce champ est nullable

        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'login.required' => 'Le login est obligatoire.',
            'login.unique' => 'Cet login est déjà utilisé.',
            'role.required' => 'Le rôle est obligatoire.',
            'etat.required' => 'L\'etat est obligatoire.',
            // 'role.in' => 'Le rôle doit être parmi les valeurs autorisées.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     *
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
{
    throw new HttpResponseException($this->sendResponse(
        $validator->errors()->toArray(),
        StateEnums::ECHEC->value,  
        422
    ));
}


    /**
     * Format the response for validation errors.
     *
     * @param array $errors
     * @param string $status
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function sendResponse(array $errors, string $status, int $statusCode): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => 'Validation failed',
            'data' => $errors
        ], $statusCode);
    }
}
