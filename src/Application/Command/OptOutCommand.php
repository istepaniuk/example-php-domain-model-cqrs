<?php

declare(strict_types=1);

namespace Newsletter\Application\Command;

use Newsletter\Domain\Subscriber\EmailAddress;

final class OptOutCommand
{
    private EmailAddress $emailAddress;

    public function __construct(string $emailAddress)
    {
        $this->emailAddress = EmailAddress::fromString($emailAddress);
    }

    public function emailAddress(): EmailAddress
    {
        return $this->emailAddress;
    }
}
