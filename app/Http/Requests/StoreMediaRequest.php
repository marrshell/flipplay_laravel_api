<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $mediaParam = $this->route('medium') ?? $this->route('media');
        $mediaId = is_object($mediaParam) ? $mediaParam->id : $mediaParam;
        $isPatch = $this->isMethod('PATCH') || $this->isMethod('PUT');

        $titleRule = Rule::unique('media', 'title');
        if ($mediaId) {
            $titleRule = $titleRule->ignore($mediaId);
        }

        return [
            'title' => $isPatch
                ? ['sometimes', 'required', 'string', 'max:255', $titleRule]
                : ['required', 'string', 'max:255', $titleRule],
            'type' => $isPatch
                ? ['sometimes', 'required', 'string', 'max:100']
                : ['required', 'string', 'max:100'],
            'creator' => $isPatch
                ? ['sometimes', 'required', 'string', 'max:255']
                : ['required', 'string', 'max:255'],
            'year' => $isPatch
                ? ['sometimes', 'required', 'integer', 'min:1900', 'max:2100']
                : ['required', 'integer', 'min:1900', 'max:2100'],
            'duration' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul media wajib diisi.',
            'title.unique' => 'Judul media sudah ada.',
            'creator.required' => 'Creator/sutradara wajib diisi.',
            'year.required' => 'Tahun rilis wajib diisi.',
        ];
    }
}
