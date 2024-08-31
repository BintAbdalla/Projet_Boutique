<?php

namespace App\Http\Requests;

use App\Enums\RoleEnums;
use App\Enums\StateEnums;
// use App\Rules\CustumPasswordRule;
use App\Rules\CustumPasswordRules;
use App\Rules\TelephoneRules;
use App\Traits\Responsetrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;


class StoreClientRequests extends FormRequest
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
        $rules = [
            'surname' => ['required', 'string', 'max:255','unique:clients,surname'],
            'address' => ['string', 'max:255'],
            'telephone' => ['required',new TelephoneRules()],

            'user' => ['sometimes','array'],
            'user.nom' => ['required_with:user','string'],
            'user.prenom' => ['required_with:user','string'],
            'user.login' => ['required_with:user','string'],
            'user.role' => ['nullable', 'exists:roles,role'],
            'user.password' => ['required_with:user', new CustumPasswordRules(),'confirmed'],

        ];
/*
        
*/
      //  dd($rules);

        return $rules;
    }

    function messages()
    {
        return [
            'surname.required' => "Le surnom est obligatoire.",
        ];
    }

    function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->sendResponse($validator->errors(),StateEnums::ECHEC,404));
    }
}

// if ($this->filled('user')) {
//     $userRules = (new StoreUserRequest())->Rules();
//     $rules = array_merge($rules, ['user' => 'array']);
//     $rules = array_merge($rules, array_combine(
//         array_map(fn($key) => "user.$key", array_keys($userRules)),
//         $userRules
//     ));
// }