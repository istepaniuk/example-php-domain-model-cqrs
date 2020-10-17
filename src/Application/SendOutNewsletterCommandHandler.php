<?php

declare(strict_types=1);

namespace Newsletter\Application;

use Newsletter\Application\Command\SendOutNewsletterCommand;
use Newsletter\Domain\NewsletterSender;
use Newsletter\Domain\Subscriber\SubscriberRepository;

final class SendOutNewsletterCommandHandler
{
    private SubscriberRepository $subscriberRepository;
    private NewsletterSender $sender;

    public function __construct(
        SubscriberRepository $repository,
        NewsletterSender $sender
    ) {
        $this->subscriberRepository = $repository;
        $this->sender = $sender;
    }

    public function handle(SendOutNewsletterCommand $command): void
    {
        $subscribers = $this->subscriberRepository->all();

        foreach ($subscribers as $subscriber) {
            if ($subscriber->isSubscribed()) {
                $this->sender->send($command->newsletter(), $subscriber);
            }
        }
    }
}
