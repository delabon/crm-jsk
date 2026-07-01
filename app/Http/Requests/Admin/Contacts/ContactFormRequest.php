<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Contacts;

use App\DataTransferObjects\Contacts\ContactFormDto;
use App\Enums\ContactStatus;
use App\Models\Contact;
use App\Rules\ValidAccountAssignment;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ContactFormRequest extends FormRequest
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
        /** @var ?Contact $contact */
        $contact = $this->route('contact');

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
                $contact
                    ? Rule::unique('contacts', 'email')->ignore($contact->id)
                    : Rule::unique('contacts', 'email')
            ],
            'status' => [
                'required',
                Rule::enum(ContactStatus::class),
            ],
            'account_id' => [
                'nullable',
                'exists:accounts,id',
                new ValidAccountAssignment($this->user(), $this->integer('account_id'))
            ]
        ];
    }

    public function toDto(): ContactFormDto
    {
        $accountId = $this->integer('account_id', 0);

        return new ContactFormDto(
            firstName: $this->string('first_name')->toString(),
            lastName: $this->string('last_name')->toString(),
            phone: $this->string('phone')->toString(),
            status: $this->enum('status', ContactStatus::class),
            email: $this->input('email'),
            accountId: $accountId === 0
                ? null
                : $accountId,
        );
    }
}
