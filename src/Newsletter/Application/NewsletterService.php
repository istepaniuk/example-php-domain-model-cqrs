<?php

namespace Newsletter\Domain;

class NewsletterService
{
    private $subscriberRepository;
    private $clock;
    private $sender;

    public function __construct(
        SubscriberRepository $subscriberRepository,
        Clock $clock,
        NewsletterSender $sender)
    {
        $this->subscriberRepository = $subscriberRepository;
        $this->clock = $clock;
        $this->sender = $sender;
    }

    public function signUp(EmailAddress $emailAddress, SubscriberName $name)
    {
        $id = SubscriberId::generate();
        $subscriber = new Subscriber($id, $emailAddress, $name);
        $this->subscriberRepository->save($subscriber);
    }

    public function optOutSubscriber(EmailAddress $emailAddress)
    {
        $subscriber = $this->subscriberRepository->getByEmailAddress($emailAddress);
        $now = $this->clock->utcNow();
        $subscriber->optOut($now);
        $this->subscriberRepository->save($subscriber);
    }

    public function sendNewsletterToAllSubscribers(Newsletter $newsletter)
    {
        $subscribers = $this->subscriberRepository->getAll();
        foreach ($subscribers as $subscriber) {
            $this->sender->sendNewsletter($subscriber, $newsletter);
        }
    }
}