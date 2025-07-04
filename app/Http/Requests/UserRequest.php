<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            //'password' => 'required|string|min:8',
        ];

        if($this->request->has('user_id')){
            $rules['email'] = ['required', 'email', Rule::unique('users')->ignore($this->request->get('user_id'))];
        }

        return $rules;
    }

        public function messages()
    {
        return [
            'name.required' => 'Il campo nome è obbligatorio.',
            'surname.required' => 'Il campo cognome è obbligatorio.',
            'email.required' => 'Il campo email è obbligatorio.',
            //'password.required' => 'Il campo password è obbligatorio.',
        ];
    }
}
