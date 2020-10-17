<?php

declare(strict_types=1);

namespace Newsletter\Application;

use Newsletter\Application\Command\OptOutCommand;
use Newsletter\Domain\Clock;
use Newsletter\Domain\Subscriber\SubscriberRepository;

final class OptOutCommandHandler
{
    private SubscriberRepository $repository;
    private SubscriberEmailDirectory $directory;
    private Clock $clock;

    public function __construct(
        SubscriberRepository $repository,
        SubscriberEmailDirectory $directory,
        Clock $clock
    ) {
        $this->repository = $repository;
        $this->directory = $directory;
        $this->clock = $clock;
    }

    public function handle(OptOutCommand $command): void
    {
        $subscriberId = $this->directory->getSubscriberIdByEmailAddress($command->emailAddress());
        $subscriber = $this->repository->get($subscriberId);
        $subscriber->optOut($this->clock->utcNow());
        $this->repository->save($subscriber);
    }
}
