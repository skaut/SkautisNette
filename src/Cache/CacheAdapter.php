<?php

declare(strict_types=1);

namespace Skaut\SkautisNette\Cache;

use DateInterval;
use DateTimeImmutable;
use Nette\Caching\Cache;
use Psr\SimpleCache\CacheInterface;
use Throwable;

/**
 * PSR-16 adapter over nette/caching, meant for Skaut\Skautis\Wsdl\Decorator\Cache\CacheDecorator.
 *
 * Nette cannot store null, so a stored null and a missing item are the same thing: has() returns false
 * and get() returns the default. Write failures of the storage make set(), delete() and clear() return false.
 */
class CacheAdapter implements CacheInterface
{
    private const string RESERVED_CHARACTERS = '{}()/\\@:';

    /**
     * @param int|null $defaultTtl seconds an item lives when set() gets no TTL; null = until the storage drops it
     *
     * @throws InvalidTTLException
     */
    public function __construct(
        private readonly Cache $cache,
        private readonly ?int $defaultTtl = null,
    ) {
        if ($defaultTtl !== null && $defaultTtl <= 0) {
            throw new InvalidTTLException('Default TTL must be a positive number of seconds or null.');
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->assertValidKey($key);

        try {
            return $this->cache->load($key) ?? $default;
        } catch (Throwable $exception) {
            throw new CacheException("Failed to load key '$key'.", $exception);
        }
    }

    public function set(string $key, mixed $value, null|int|DateInterval $ttl = null): bool
    {
        $this->assertValidKey($key);
        $seconds = $this->ttlToSeconds($ttl);

        try {
            if ($seconds !== null && $seconds <= 0) {
                // PSR-16: a zero or negative TTL means the item is expired already
                $this->cache->remove($key);
            } else {
                $this->cache->save($key, $value, $seconds === null ? null : [Cache::Expire => $seconds]);
            }
        } catch (Throwable) {
            return false;
        }

        return true;
    }

    public function delete(string $key): bool
    {
        $this->assertValidKey($key);

        try {
            $this->cache->remove($key);
        } catch (Throwable) {
            return false;
        }

        return true;
    }

    public function clear(): bool
    {
        try {
            $this->cache->clean([Cache::All => true]);
        } catch (Throwable) {
            return false;
        }

        return true;
    }

    /**
     * @param iterable<mixed> $keys
     *
     * @return array<string, mixed>
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $keys = $this->toKeyList($keys);

        try {
            $values = $this->cache->bulkLoad($keys);
        } catch (Throwable $exception) {
            throw new CacheException('Failed to load keys '.implode(', ', $keys).'.', $exception);
        }

        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $values[$key] ?? $default;
        }

        return $result;
    }

    /**
     * @param iterable<mixed, mixed> $values
     */
    public function setMultiple(iterable $values, null|int|DateInterval $ttl = null): bool
    {
        $succeeded = true;
        foreach ($values as $key => $value) {
            // PHP turns numeric string keys of an array into integers
            $key = \is_int($key) ? (string) $key : $key;
            if (! \is_string($key)) {
                throw new InvalidKeyException('Cache key must be a string, '.get_debug_type($key).' given.');
            }

            if (! $this->set($key, $value, $ttl)) {
                $succeeded = false;
            }
        }

        return $succeeded;
    }

    /**
     * @param iterable<mixed> $keys
     */
    public function deleteMultiple(iterable $keys): bool
    {
        $succeeded = true;
        foreach ($this->toKeyList($keys) as $key) {
            if (! $this->delete($key)) {
                $succeeded = false;
            }
        }

        return $succeeded;
    }

    public function has(string $key): bool
    {
        $this->assertValidKey($key);

        try {
            return $this->cache->load($key) !== null;
        } catch (Throwable $exception) {
            throw new CacheException("Failed to load key '$key'.", $exception);
        }
    }

    /**
     * @param iterable<mixed> $keys
     *
     * @return list<string>
     *
     * @throws InvalidKeyException
     */
    private function toKeyList(iterable $keys): array
    {
        $list = [];
        foreach ($keys as $key) {
            if (! \is_string($key)) {
                throw new InvalidKeyException('Cache key must be a string, '.get_debug_type($key).' given.');
            }
            $this->assertValidKey($key);
            $list[] = $key;
        }

        return $list;
    }

    /**
     * @return int|null null = no expiration
     */
    private function ttlToSeconds(null|int|DateInterval $ttl): ?int
    {
        if ($ttl === null) {
            return $this->defaultTtl;
        }

        if ($ttl instanceof DateInterval) {
            $now = new DateTimeImmutable();

            return $now->add($ttl)->getTimestamp() - $now->getTimestamp();
        }

        return $ttl;
    }

    /**
     * @throws InvalidKeyException
     */
    private function assertValidKey(string $key): void
    {
        if ($key === '') {
            throw new InvalidKeyException('Cache key must be at least one character long.');
        }

        if (strpbrk($key, self::RESERVED_CHARACTERS) !== false) {
            throw new InvalidKeyException("Cache key '$key' contains one of the reserved characters ".self::RESERVED_CHARACTERS.'.');
        }
    }
}
