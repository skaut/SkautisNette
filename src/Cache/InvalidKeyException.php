<?php

declare(strict_types=1);

namespace Skaut\SkautisNette\Cache;

/**
 * The key is not a valid PSR-16 cache key.
 */
class InvalidKeyException extends InvalidArgumentException
{
}
