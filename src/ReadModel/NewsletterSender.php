<?php

declare(strict_types=1);

namespace Newsletter\ReadModel;

interface NewsletterSender
{
    public function send(Newsletter $newsletter, string $emailAddress): void;
}
