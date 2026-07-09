<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard\Settings;

use App\Concerns\ProfileValidationRules;
use App\DataTransferObjects\Users\UpdateProfileDto;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class ProfileUpdateRequest extends FormRequest
{
    use ProfileValidationRules;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return $this->profileRules($this->user()->id);
    }

    public function toDto(): UpdateProfileDto
    {
        return new UpdateProfileDto(
            firstName: $this->string('first_name')->value(),
            lastName: $this->string('last_name')->value(),
            email: $this->string('email')->value()
        );
    }
}
