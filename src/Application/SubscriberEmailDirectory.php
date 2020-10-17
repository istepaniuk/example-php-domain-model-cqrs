<?php

declare(strict_types=1);

namespace Newsletter\Application;

use Newsletter\Domain\Subscriber\EmailAddress;
use Newsletter\Domain\Subscriber\SubscriberId;

interface SubscriberEmailDirectory
{
    public function getSubscriberIdByEmailAddress(EmailAddress $emailAddress): SubscriberId;

    public function isEmailAddressAlreadyInUse(EmailAddress $emailAddress): bool;
}
