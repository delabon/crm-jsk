<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Users;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\DataTransferObjects\StoreUserDto;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreUserRequest extends FormRequest
{
    use PasswordValidationRules,
        ProfileValidationRules;

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
        /** @var User|null $user */
        $user = $this->route('user');

        return [
            ...$this->profileRules($user?->id),
            'role' => [
                'required',
                'string',
                Rule::enum(UserRole::class),
            ],
            'password' => $this->passwordRules(),
        ];
    }

    public function toDto(): StoreUserDto
    {
        return new StoreUserDto(
            firstName: $this->string('first_name')->value(),
            lastName: $this->string('last_name')->value(),
            email: $this->string('email')->value(),
            password: $this->string('password')->value(),
            role: $this->enum('role', UserRole::class),
        );
    }
}
