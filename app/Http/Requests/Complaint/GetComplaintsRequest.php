<?php

namespace App\Http\Requests\Complaint;

use Illuminate\Foundation\Http\FormRequest;

class GetComplaintsRequest extends FormRequest
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
            'type' => 'nullable|sometimes|in:all,user,post,comment,message',
            'date' => 'nullable|sometimes|date',
            'status' => 'nullable|sometimes|in:checked,non-checked',
            'measure_status' => 'nullable|sometimes|in:accepted,rejected',
            'page_id' => 'required|integer'
        ];
    }
}
