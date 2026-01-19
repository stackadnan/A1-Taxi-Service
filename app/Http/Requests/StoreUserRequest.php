<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasPermission('user.create');
    }

    public function rules(): array
    {
        return [
            'name' => ['required','string','max:255'],
            'username' => ['required','string','max:255','unique:users,username'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['nullable','string','min:6','confirmed'],
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id'],
            'is_active' => ['nullable','boolean'],
        ];
    }
}