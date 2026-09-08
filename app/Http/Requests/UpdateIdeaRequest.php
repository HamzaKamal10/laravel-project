<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpdateIdeaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('idea'));
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'status' => 'sometimes|required|string',
            'image' => ['nullable', File::image()->max('5mb')],
            'links' => 'nullable|array',
            'links.*' => 'required|url|max:255',
            'steps' => 'nullable|array',
            'steps.*.id' => ['nullable', 'integer'],
            'steps.*.description' => ['required', 'string', 'max:255'],
            'steps.*.completed' => ['required', 'boolean'],
        ];
    }
}
