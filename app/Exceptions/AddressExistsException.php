<?php

declare(strict_types=1);

namespace App\Exceptions;

use LogicException;

final class AddressExistsException extends LogicException
{
    public static function withContactId(int $id): self
    {
        return new self(sprintf('The address already exists for contact with the id: %d.', $id));
    }
}
