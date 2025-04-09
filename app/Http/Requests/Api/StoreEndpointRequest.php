<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreEndpointRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'method' => 'required|string|in:GET,POST,PUT,DELETE',
            'headers' => 'string|max:1024',
            'body' => 'string|max:1024',
            'user_id' => 'required|exists:users,id',
            'check_interval' => 'required|integer|min:1',
            'alert_threshold' => 'integer:min:50',
            'username' => 'string|max:150',
            'password' => 'string|max:150',
        ];
    }
}
