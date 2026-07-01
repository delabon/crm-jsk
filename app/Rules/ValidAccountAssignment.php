<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Account;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

final readonly class ValidAccountAssignment implements ValidationRule
{
    public function __construct(
        private User $user,
        private int $accountId
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $account = Account::find($this->accountId);

        if (! $account) {
            $fail('The account does not exist.');

            return;
        }

        if ($this->user->can('accounts.view-any')) {
            return;
        }

        if ($this->user->can('view', [$account])) {
            return;
        }

        $fail('You do not have permission to assign this account.');
    }
}
