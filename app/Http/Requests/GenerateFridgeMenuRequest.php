<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateFridgeMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'one' => trim((string) $this->input('one')),
            'two' => trim((string) $this->input('two')),
            'one_slug' => filled($this->input('one_slug')) ? $this->input('one_slug') : null,
            'two_slug' => filled($this->input('two_slug')) ? $this->input('two_slug') : null,
        ]);
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'one' => ['required', 'string', 'min:2', 'max:80'],
            'two' => ['required', 'string', 'min:2', 'max:80', 'different:one'],
            'one_slug' => ['nullable', 'string', 'max:120'],
            'two_slug' => ['nullable', 'string', 'max:120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'one' => 'eerste product',
            'two' => 'tweede product',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'two.different' => 'Kies twee verschillende producten.',
        ];
    }
}
