<?php

namespace App\Http\Requests\PrivacySettings;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePrivacySettingsRequest extends FormRequest
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
            'account_type' => 'required|string',
            'who_can_comment' => 'required|string',
            'who_can_repost' => 'required|string',
            'who_can_message' => 'required|string'
        ];
    }
}
