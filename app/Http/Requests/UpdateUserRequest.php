<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasPermission('user.edit');
    }

    public function rules(): array
    {
        $userId = $this->route('user') ? $this->route('user')->id : null;

        return [
            'name' => ['required','string','max:255'],
            'username' => ['required','string','max:255','unique:users,username,' . $userId],
            'email' => ['required','email','max:255','unique:users,email,' . $userId],
            'password' => ['nullable','string','min:6','confirmed'],
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id'],
            'is_active' => ['nullable','boolean'],
        ];
    }
}