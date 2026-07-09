<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Users;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\DataTransferObjects\Users\UpdateUserDto;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateUserRequest extends FormRequest
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
        ];
    }

    public function toDto(): UpdateUserDto
    {
        return new UpdateUserDto(
            firstName: $this->string('first_name')->value(),
            lastName: $this->string('last_name')->value(),
            email: $this->string('email')->value(),
            role: $this->enum('role', UserRole::class)
        );
    }
}
