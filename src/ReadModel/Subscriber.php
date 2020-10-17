<?php

declare(strict_types=1);

namespace Newsletter\ReadModel;

final class Subscriber
{
    public string $id;
    public string $firstName;
    public string $lastName;
    public string $emailAddress;
    public bool $optedOut;

    public function __construct(
        string $id,
        string $firstName,
        string $lastName,
        string $emailAddress,
        bool $optedOut
    ) {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->emailAddress = $emailAddress;
        $this->optedOut = $optedOut;
    }
}
