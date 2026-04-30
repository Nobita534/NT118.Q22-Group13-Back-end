<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ArticleIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
            'sort' => ['sometimes', 'in:published_at_desc,published_at_asc,views_desc'],
            'category' => ['sometimes', 'string'],
            'tag' => ['sometimes', 'string'],
            'q' => ['sometimes', 'string'],
        ];
    }
}
