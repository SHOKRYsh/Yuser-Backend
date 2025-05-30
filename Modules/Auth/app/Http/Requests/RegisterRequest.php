<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
         return [
            'name'          => 'required|string|max:255',
            'email'         => 'required_without:phone|nullable|email|unique:users,email',
            'country_code'  => 'nullable|string|max:10',
            'phone'          => 'required_without:email|nullable|string|max:20|unique:users,phone',
            'password'      => 'required|string|min:6|confirmed',
            'gender'        => 'nullable|in:male,female',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'role'          => 'nullable|exists:roles,name',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
