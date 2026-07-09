<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard\Accounts;

use App\DataTransferObjects\Accounts\AccountFilterDto;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class IndexAccountRequest extends FormRequest
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

    public function toDto(): AccountFilterDto
    {
        return new AccountFilterDto(
            search: $this->string('search')->value(),
        );
    }
}
