<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
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
            'chat_id' => 'required|integer',
            'text' => 'nullable|string|max:1024',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,avi,mkv,mp3,wav,ogg,pdf,docx,doc,xlsx,xls,pptx,ppt,txt,csv,md,xml,json,rtf,odt,ods,zip,tar,gz|max:10240',
        ];
    }
}
