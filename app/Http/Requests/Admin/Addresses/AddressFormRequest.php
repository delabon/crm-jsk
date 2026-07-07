<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Addresses;

use App\DataTransferObjects\Addresses\SaveAddressDto;
use App\Rules\ValidRegion;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Squire\Rules\CountryRule;

final class AddressFormRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'line1' => [
                'required',
                'string',
                'max:255',
            ],
            'line2' => [
                'nullable',
                'string',
                'max:255',
            ],
            'city' => [
                'required',
                'string',
                'max:255',
            ],
            'country_id' => [
                'required',
                'string',
                'max:3',
                new CountryRule('id'),
            ],
            'region_id' => [
                'nullable',
                new ValidRegion($this->input('country_id')),
            ],
            'postal_code' => [
                'required',
                'string',
                'max:15',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'region_id' => 'state',
            'country_id' => 'country',
        ];
    }

    public function toDto(): SaveAddressDto
    {
        return SaveAddressDto::fromArray($this->validated());
    }
}
