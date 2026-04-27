<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Contacts;

use App\DataTransferObjects\Contacts\ContactFormDto;
use App\Enums\ContactStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreContactRequest extends FormRequest
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
            'first_name' => [
                'required',
                'string',
                'max:255',
            ],
            'last_name' => [
                'required',
                'string',
                'max:255',
            ],
            'phone' => [
                'required',
                'string',
                'max:30',
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                'unique:contacts,email',
            ],
            'status' => [
                'required',
                Rule::enum(ContactStatus::class),
            ]
        ];
    }

    public function toDto(): ContactFormDto
    {
        return new ContactFormDto(
            firstName: $this->string('first_name')->toString(),
            lastName: $this->string('last_name')->toString(),
            phone: $this->string('phone')->toString(),
            status: $this->enum('status', ContactStatus::class),
            email: $this->input('email'),
        );
    }
}
