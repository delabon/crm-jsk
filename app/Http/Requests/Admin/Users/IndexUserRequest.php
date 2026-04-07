<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Users;

use App\DataTransferObjects\UserFilterDto;
use App\Enums\Role;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class IndexUserRequest extends FormRequest
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
            'role' => [
                'nullable',
                'string',
                'in:'.$this->allowedRoles(),
            ],
            'verified' => [
                'nullable',
                'string',
                'in:all,yes,no',
            ],
            'search' => [
                'nullable',
                'string',
                'min:1',
                'max:255',
            ],
        ];
    }

    private function allowedRoles(): string
    {
        $roles = array_column(Role::options(), 'value');
        array_unshift($roles, 'all');

        return implode(',', $roles);
    }

    public function toDto(): UserFilterDto
    {
        return new UserFilterDto(
            search: $this->string('search')->value(),
            verified: $this->string('verified')->value(),
            role: $this->string('role')->value(),
        );
    }
}
