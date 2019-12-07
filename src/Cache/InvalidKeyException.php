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


  /**
   * @param string $validationError
   * @param mixed $key
   */
  public function __construct(
    string $validationError,
    $key
  ) {
    parent::__construct("$validationError Found: \"$key\"");
  }
}
