<?php

declare(strict_types=1);

namespace Newsletter\Application;

use Newsletter\Application\Command\SignUpCommand;
use Newsletter\Domain\Subscriber\Subscriber;
use Newsletter\Domain\Subscriber\SubscriberEmailAddressAlreadyInUse;
use Newsletter\Domain\Subscriber\SubscriberId;
use Newsletter\Domain\Subscriber\SubscriberRepository;

final class SignUpCommandHandler
{
    private SubscriberRepository $subscriberRepository;
    private SubscriberEmailDirectory $directory;
    private Clock $clock;

    public function __construct(
        SubscriberRepository $subscriberRepository,
        SubscriberEmailDirectory $directory,
        Clock $clock
    ) {
        $this->subscriberRepository = $subscriberRepository;
        $this->directory = $directory;
        $this->clock = $clock;
    }

    public function handle(SignUpCommand $command): void
    {
        if ($this->directory->isEmailAddressAlreadyInUse($command->emailAddress())) {
            throw new SubscriberEmailAddressAlreadyInUse();
        }

        $subscriber = Subscriber::signUp(
            SubscriberId::generate(),
            $command->emailAddress(),
            $command->subscriberName(),
            $this->clock->utcNow()
        );

        $this->subscriberRepository->save($subscriber);
    }
}
