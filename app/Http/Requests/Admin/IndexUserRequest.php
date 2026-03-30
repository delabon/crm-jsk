<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

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
        ];
    }

    private function allowedRoles(): string
    {
        $roles = array_column(Role::options(), 'value');
        array_unshift($roles, 'all');

        return implode(',', $roles);
    }
}
