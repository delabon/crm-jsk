<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Concerns\PasswordValidationRules;
use App\DataTransferObjects\ResetPasswordDto;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class ResetPasswordRequest extends FormRequest
{
    use PasswordValidationRules;

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
            'email' => [
                'required',
                'email',
                'exists:users,email',
            ],
            'token' => [
                'required',
                'string',
            ],
            'password' => $this->passwordRules(),
        ];
    }

    public function toDto(): ResetPasswordDto
    {
        return new ResetPasswordDto(
            email: $this->string('email')->value(),
            token: $this->string('token')->value(),
            password: $this->string('password')->value()
        );
    }
}
