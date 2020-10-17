<?php

declare(strict_types=1);

namespace Newsletter\Application;

use DateTimeInterface;

interface Clock
{
    public function utcNow(): DateTimeInterface;
}
