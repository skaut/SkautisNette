<?php

declare(strict_types=1);


namespace Skautis\Nette\Cache;


class CacheException
  extends
  \RuntimeException
  implements
  \Psr\SimpleCache\InvalidArgumentException
{

  public function __construct(
    string $message,
    \Throwable $previous
  ) {
    parent::__construct($message, 0, $previous);
  }
}