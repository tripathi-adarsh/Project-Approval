<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'file'        => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,zip', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimes' => 'Allowed: PDF, DOC, DOCX, XLS, XLSX, ZIP.',
            'file.max'   => 'Max file size is 10 MB.',
        ];
    }
}
