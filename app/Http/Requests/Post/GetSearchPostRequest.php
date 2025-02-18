<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class GetSearchPostRequest extends FormRequest
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
            'user' => 'nullable|string|max:64',
            'location' => 'nullable|string|max:64',
            'text' => 'nullable|string|max:512',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:32',
            'page_id' => 'required|integer'
        ];
    }
}
