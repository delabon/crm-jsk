<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Accounts;

use App\DataTransferObjects\StoreAccountDto;
use Illuminate\Foundation\Http\FormRequest;

final class StoreAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'industry' => [
                'required',
                'string',
                'max:255',
            ],
            'website' => [
                'required',
                'string',
                'max:255',
                'url',
            ],
            'phone' => [
                'required',
                'string',
                'max:30',
            ],
        ];
    }

    public function toDto(): StoreAccountDto
    {
        return new StoreAccountDto(
            name: $this->string('name')->value(),
            industry: $this->string('industry')->value(),
            website: $this->string('website')->value(),
            phone: $this->string('phone')->value(),
        );
    }
}
