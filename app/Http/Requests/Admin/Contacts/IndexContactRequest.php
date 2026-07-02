<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Contacts;

use App\DataTransferObjects\Contacts\ContactFilterDto;
use App\Enums\ContactStatus;
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
            'status' => [
                'nullable',
                'in:'.$this->allowedStatuses(),
            ],
        ];
    }

    public function toDto(): ContactFilterDto
    {
        return new ContactFilterDto(
            search: $this->string('search')->value(),
            status: $this->enum('status', ContactStatus::class),
        );
    }

    private function allowedStatuses(): string
    {
        return implode(',', [
            'all',
            ...array_column(ContactStatus::cases(), 'value'),
        ]);
    }
}
