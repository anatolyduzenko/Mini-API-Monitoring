<?php

namespace App\Http\Requests\Api;

use App\Enums\AuthenticationType;
use App\Enums\RequestType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateEndpointRequest extends FormRequest
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
            'method' => [Rule::enum(RequestType::class)],
            'headers' => 'string|nullable|max:1024',
            'body' => 'string|nullable|max:1024',
            'user_id' => 'required|exists:users,id',
            'check_interval' => 'required|integer|min:1',
            'alert_threshold' => 'integer|min:50',
            'username' => 'string|nullable|max:150',
            'password' => 'string|nullable|max:150',
            'auth_type' => [Rule::enum(AuthenticationType::class)],
            'auth_token' => 'string|nullable|max:150',
            'auth_token_name' => 'string|nullable|max:150',
            'auth_url' => 'url|nullable',
            'dashboard_visible' => 'boolean|nullable',
        ];
    }
}
