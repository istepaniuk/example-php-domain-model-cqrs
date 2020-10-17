<?php

declare(strict_types=1);

namespace Newsletter\ReadModel;

interface SubscriberRepository
{
    public function save(Subscriber $subscriber): void;

    /**
     * @throws SubscriberNotFoundException
     */
    public function get(string $id): Subscriber;

    /**
     * @return Subscriber[]
     */
    public function all(): array;
}
