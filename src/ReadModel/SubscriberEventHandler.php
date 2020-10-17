<?php
declare(strict_types=1);

namespace Newsletter\ReadModel;

final class SubscriberEventHandler
{
    private SubscriberRepository $repository;

    public function __construct(SubscriberRepository $repository)
    {
        $this->repository = $repository;
    }

    public function onSignUp($event)
    {
        $subscriber = new Subscriber(
            $event->subscriberId,
            $event->subscriberFirstName,
            $event->subscriberLastName,
            $event->subscriberEmail,
            false
        );
        $this->repository->save($subscriber);
    }

    public function onOptOut($event)
    {
        $subscriber = $this->repository->get($event->subscriberId());
        $subscriber->optedOut = true;
        $this->repository->save($subscriber);
    }
}
