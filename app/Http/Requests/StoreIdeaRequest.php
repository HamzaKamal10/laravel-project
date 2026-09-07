<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\IdeaStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class StoreIdeaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['required', Rule::enum(IdeaStatus::class)],
            'image' => ['nullable', File::image()->max('5mb')],
            'links' => 'nullable|array',
            'links.*' => 'required|url|max:255',
            'steps' => 'nullable|array',
            'steps.*' => 'required|string|max:255',
        ];
    }
}
