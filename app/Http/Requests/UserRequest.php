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
        $userId = $this->route('user') ? $this->route('user')->id : null;

        return [
            'username' => ['nullable', 'string', Rule::unique('users', 'username')->ignore($userId)],
            'name' => ['nullable', 'string'],
            'email' => ['nullable', 'string', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'phone_number' => ['nullable', 'string', Rule::unique('users', 'phone_number')->ignore($userId)],
            'avatar' => ['nullable', 'url'],
            'password' => $this->isMethod('post') ? ['required', 'string', 'min:8'] : ['nullable', 'string', 'min:8'],
        ];
    }
}
