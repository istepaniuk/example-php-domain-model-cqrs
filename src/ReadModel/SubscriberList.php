<?php

declare(strict_types=1);

namespace Newsletter\ReadModel;

final class SubscriberList
{
    private SubscriberRepository $repository;

    public function __construct(SubscriberRepository $repository)
    {
        $this->repository = $repository;
    }

    public function subscribers(): array
    {
        return $this->repository->all();
    }
}
