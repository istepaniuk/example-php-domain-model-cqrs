<?php

declare(strict_types=1);

namespace Newsletter\Application\Command;

use Newsletter\Domain\Subscriber\EmailAddress;
use Newsletter\Domain\Subscriber\SubscriberName;

final class SignUpCommand
{
    private EmailAddress $emailAddress;
    private SubscriberName $subscriberName;

    public function __construct(string $emailAddress, string $firstName, string $lastName)
    {
        $this->emailAddress = EmailAddress::fromString($emailAddress);
        $this->subscriberName = SubscriberName::fromStrings($firstName, $lastName);
    }

    public function emailAddress(): EmailAddress
    {
        return $this->emailAddress;
    }

    public function subscriberName(): SubscriberName
    {
        return $this->subscriberName;
    }
}
