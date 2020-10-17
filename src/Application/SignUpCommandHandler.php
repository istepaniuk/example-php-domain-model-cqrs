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

    public function __construct(SubscriberRepository $subscriberRepository, SubscriberEmailDirectory $directory)
    {
        $this->subscriberRepository = $subscriberRepository;
        $this->directory = $directory;
    }

    public function handle(SignUpCommand $command): void
    {
        if($this->directory->isEmailAddressAlreadyInUse($command->emailAddress())) {
            throw new SubscriberEmailAddressAlreadyInUse();
        }
        $id = SubscriberId::generate();
        $subscriber = Subscriber::create($id, $command->emailAddress(), $command->subscriberName());
        $this->subscriberRepository->save($subscriber);
    }
}
