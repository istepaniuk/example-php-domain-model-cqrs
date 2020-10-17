<?php

declare(strict_types=1);

namespace Newsletter\Application\Command;

use Newsletter\Domain\Newsletter;

final class SendOutNewsletterCommand
{
    private Newsletter $newsletter;

    public function __construct(string $newsletterSubject, string $newsletterBody)
    {
        $this->newsletter = Newsletter::fromStrings($newsletterSubject, $newsletterBody);
    }

    public function newsletter(): Newsletter
    {
        return $this->newsletter;
    }
}
