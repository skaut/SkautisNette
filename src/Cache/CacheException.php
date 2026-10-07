<?php

declare(strict_types=1);

namespace Skaut\SkautisNette\Cache;

use RuntimeException;
use Throwable;

/**
 * The underlying Nette storage failed.
 */
class CacheException extends RuntimeException implements \Psr\SimpleCache\CacheException
{
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
