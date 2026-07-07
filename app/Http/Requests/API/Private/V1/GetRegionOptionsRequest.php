<?php

declare(strict_types=1);

namespace App\Http\Requests\API\Private\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Squire\Rules\CountryRule;

final class GetRegionOptionsRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'country_id' => [
                'nullable',
                'string',
                new CountryRule('id'),
            ],
        ];
    }
}
