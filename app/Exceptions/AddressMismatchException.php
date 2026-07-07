<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Models\Account;
use App\Models\Contact;
use LogicException;

final class AddressMismatchException extends LogicException
{
    public static function forModel(Account|Contact $model): self
    {
        return new self(sprintf(
            'Address does not belong to the passed model [%s#%d].',
            $model::class,
            $model->id,
        ));
    }
}
