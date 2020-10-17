<?php
declare(strict_types=1);

namespace Newsletter\Infrastructure;

final class SimpleCommandDispatcher
{
    private array $commandHandlers;

    public function __construct(array $commandHandlers)
    {
        $this->commandHandlers = $commandHandlers;
    }

    public function dispatch(object $command): void
    {
        foreach ($this->commandHandlers as $commandHandler){
            // magic.
            // $this->commandHandlers->handle($command);
        }
    }
}
