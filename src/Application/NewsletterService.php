<?php

declare(strict_types=1);

namespace Newsletter\Application;

use Newsletter\Domain\Clock;
use Newsletter\Domain\Newsletter;
use Newsletter\Domain\NewsletterSender;
use Newsletter\Domain\Subscriber\EmailAddress;
use Newsletter\Domain\Subscriber\Subscriber;
use Newsletter\Domain\Subscriber\SubscriberId;
use Newsletter\Domain\Subscriber\SubscriberName;
use Newsletter\Domain\Subscriber\SubscriberRepository;

final class NewsletterService
{
    private SubscriberRepository $subscriberRepository;
    private Clock $clock;
    private NewsletterSender $sender;

    public function __construct(
        SubscriberRepository $subscriberRepository,
        Clock $clock,
        NewsletterSender $sender
    ) {
        $this->subscriberRepository = $subscriberRepository;
        $this->clock = $clock;
        $this->sender = $sender;
    }

    public function signUp(EmailAddress $emailAddress, SubscriberName $name): void
    {
        $id = SubscriberId::generate();
        $subscriber = Subscriber::create($id, $emailAddress, $name);
        $this->subscriberRepository->save($subscriber);
    }

    public function optOutSubscriber(EmailAddress $emailAddress): void
    {
        $subscriber = $this->subscriberRepository->getByEmailAddress($emailAddress);
        $now = $this->clock->utcNow();
        $subscriber->optOut($now);
        $this->subscriberRepository->save($subscriber);
    }

    public function sendNewsletterToAllSubscribers(Newsletter $newsletter): void
    {
        $subscribers = $this->subscriberRepository->all();
        foreach ($subscribers as $subscriber) {
            if ($subscriber->isSubscribed()) {
                $this->sender->send($newsletter, $subscriber);
            }
        }
    }
}