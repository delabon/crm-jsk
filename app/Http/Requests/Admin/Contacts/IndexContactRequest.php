<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Contacts;

use App\DataTransferObjects\Contacts\ContactFilterDto;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class IndexContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => [
                'nullable',
                'string',
                'min:1',
                'max:255',
            ],
        ];
    }

    public function toDto(): ContactFilterDto
    {
        return new ContactFilterDto(
            search: $this->string('search')->value(),
        );
    }
}
