<?php

declare(strict_types=1);

namespace Skautis\Nette\Cache;

use Psr\SimpleCache\InvalidArgumentException;

class InvalidTTLException
  extends
  \RuntimeException
  implements
  InvalidArgumentException
{

}