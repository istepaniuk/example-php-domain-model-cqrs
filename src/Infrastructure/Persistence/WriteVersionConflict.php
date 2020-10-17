<?php

declare(strict_types=1);

namespace Newsletter\Infrastructure\Persistence;

use Exception;

final class WriteVersionConflict extends Exception
{
}
