<?php

declare(strict_types=1);

namespace Skautis\Nette\Cache;

use Psr\SimpleCache\InvalidArgumentException;

class InvalidKeyException
  extends
  \RuntimeException
  implements
  InvalidArgumentException
{


  public function __construct(
    string $validationError,
    string $key
  ) {
    parent::__construct("$validationError Found: \"$key\"");
  }
}