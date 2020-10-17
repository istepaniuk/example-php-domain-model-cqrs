<?php

declare(strict_types=1);

namespace Newsletter\Website;

use Newsletter\Application\Command\OptOutCommand;
use Newsletter\Application\Command\SignUpCommand;
use Newsletter\Domain\Subscriber\SubscriberNotFoundException;
use Newsletter\Infrastructure\SimpleCommandDispatcher;
use Newsletter\ReadModel\SubscriberList;

final class NewsletterController
{
    private SimpleCommandDispatcher $commandDispatcher;
    private SubscriberList $subscriberList;

    public function __construct()
    {
        // This could be injected using a Factory or a DI Framework, etc.
        $this->commandDispatcher  = new SimpleCommandDispatcher([
            //new OptOutCommandHandler( ... dependencies ... ),
            //new SignUpCommandHandler( ... dependencies ... )
        ]);
        $this->subscriberList = new SubscriberList(...);
    }

    public function optOutAction($emailAddress): string
    {
        try {
            $command = new OptOutCommand($emailAddress);
        } catch (\InvalidArgumentException $e) {
            return $this->render(400, 'Error400.html.twig');
        }

        try {
            $this->commandDispatcher->dispatch($command);
            return $this->render(200, 'Newsletter:opt_out_thanks.html.twig');
        } catch (SubscriberNotFoundException $e) {
            return $this->render(404, 'Newsletter:error.html.twig');
        }
    }

    public function signUp($emailAddress, $firstName, $lastName)
    {
        try {
            $command = new SignUpCommand($emailAddress, $firstName, $lastName);
        } catch (\InvalidArgumentException $e) {
            return $this->render(400, 'Error400.html.twig');
        }

        $this->commandDispatcher->dispatch($command);

        return $this->render(200, 'Newsletter:opt_out_thanks.html.twig');
    }

    public function listAllSubscribers()
    {
        $subscribers = $this->subscriberList->subscribers();

        return $this->render(200, 'Newsletter:subscriber_list.html.twig', $subscribers);
    }

    private function render(int $errorCode, string $template, $context = []): string
    {
        // renders a fancy template
        return 'something';
    }
}
