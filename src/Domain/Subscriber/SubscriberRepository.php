<?php

declare(strict_types=1);

namespace Newsletter\Domain\Subscriber;

interface SubscriberRepository
{
    public function save(Subscriber $subscriber): void;

    /**
     * @throws SubscriberNotFoundException
     */
    public function get(SubscriberId $id): Subscriber;
}
