<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BookmarkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Gộp id từ URL vào để validate
    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:Article,Article_ID',
        ];
    }
}