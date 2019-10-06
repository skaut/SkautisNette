<?php

declare(strict_types=1);

namespace Skautis\Nette\Cache;

use DateInterval;
use Exception;
use Nette\Caching\Cache;
use Psr\SimpleCache\CacheInterface;
use Tracy\Debugger;
use Traversable;


/**
 * Nette cache adapter for Skautis library
 */
class CacheAdapter
  implements
  CacheInterface
{

  /**
   * @var Cache
   */
  private $cache;

  /**
   * @var int|null
   */
  private $defaultTTL;

  public function __construct(
    Cache $cache,
    $defaultTTLSeconds = null
  ) {
    $this->cache = $cache;
    $this->defaultTTL = $defaultTTLSeconds;
  }

  /**
   * @inheritDoc
   */
  public function get(
    $key,
    $default = null
  ) {
    $this->assertValidKey($key);

    try {
      $value = $this->cache->load(
        $key,
        static function () use
        (
          $default
        ) {
          return $default;
        }
      );
    } catch (Exception $exception) {
      throw new CacheException("Failed to load key '$key''", $exception);
    }

    return $value;
  }

  /**
   * @inheritDoc
   */
  public function set(
    $key,
    $value,
    $ttl = null
  ): bool {
    $this->assertValidKey($key);

    try {
      $this->cache->save(
        $key,
        $value,
        [
          Cache::EXPIRE => $this->convertTTLToExpire($ttl),
          Cache::SLIDING => false,
        ]
      );
    } catch (Exception $exception) {
      Debugger::log($exception);

      return false;
    }

    return true;
  }

  /**
   * @inheritDoc
   */
  public function delete($key): bool
  {
    $this->assertValidKey($key);

    try {
      $this->cache->remove($key);
    } catch (Exception $exception) {
      Debugger::log($exception);

      return false;
    }

    return true;
  }

  /**
   * @inheritDoc
   */
  public function clear(): bool
  {
    try {
      $this->cache->clean();
    } catch (Exception $exception) {
      Debugger::log($exception);

      return false;
    }

    return true;
  }

  /**
   * @inheritDoc
   */
  public function getMultiple(
    $keys,
    $default = null
  ) {

    if ($keys instanceof Traversable) {
      $keys = iterator_to_array($keys, false);
    }

    if (!is_array($keys)) {
      throw new InvalidArgumentException('Keys should be an iterable of strings.');
    }

    array_map([$this, 'assertValidKey'], $keys);

    return $this->cache->bulkLoad(
      $keys,
      static function () use
      (
        $default
      ) {
        return $default;
      }
    );
  }

  /**
   * @inheritDoc
   */
  public function setMultiple(
    $values,
    $ttl = null
  ) {
    if ($values instanceof Traversable) {
      $values = iterator_to_array($values, true);
    }

    if (!is_array($values)) {
      throw new InvalidArgumentException('Values should be an iterable of key => value.');
    }

    $expire = $this->convertTTLToExpire($ttl);

    $succeeded = true;
    foreach ($values as $key => $value) {
      if (!$this->set($key, $value, $expire)) {
        $succeeded = false;
      }
    }

    return $succeeded;
  }

  /**
   * @inheritDoc
   */
  public function deleteMultiple($keys): bool
  {
    if ($keys instanceof Traversable) {
      $keys = iterator_to_array($keys, false);
    }
    if (!is_array($keys)) {
      throw new InvalidArgumentException('Keys should be an iterable of strings.');
    }

    $succeeded = true;
    foreach ($keys as $key) {
      if (!$this->delete($key)) {
        $succeeded = false;
      }
    }

    return $succeeded;
  }

  /**
   * @inheritDoc
   */
  public function has($key): bool
  {
    $this->assertValidKey($key);

    return $this->cache->load($key) !== null;
  }

  /**
   * @param null|string|DateInterval|mixed $ttl
   */
  private function convertTTLToExpire($ttl): ?string
  {
    // No TTL
    if ($ttl === null) {
      return $this->defaultTTL === null ? null : $this->defaultTTL . ' seconds';
    }

    if (is_int($ttl) && $ttl > 0) {
      return "$ttl seconds";
    }

    if ($ttl instanceof DateInterval) {
      return "{$ttl->s} seconds";
    }

    throw new InvalidTTLException(
      'TTL must be either null, positive integer representing seconds or DateInterval'
    );
  }

  /**
   * Throws an exception if $key isn't valid  PSR-16 cache key
   *
   * @param mixed|string $key
   */
  private function assertValidKey($key): void
  {
    if (!is_string($key)) {
      throw new InvalidKeyException('Cache key must be a string', $key);
    }

    if (empty($key)) {
      throw new InvalidKeyException('Cache key must be at least one character long', $key);
    }


    if (preg_match('/[{}()\/@:\\\]+/', $key)) {
      throw new InvalidKeyException(
        'Cache key must not contain any reserved characters "{}()/\@:"', $key
      );
    }
  }
}
