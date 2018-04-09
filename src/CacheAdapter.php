<?php

declare(strict_types=1);

namespace Skautis\Nette;

use Nette;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Skautis\Wsdl\Decorator\Cache\CacheInterface;


class CacheAdapter implements CacheInterface
{

	use Nette\SmartObject;

	/** @var IStorage */
	private $storage;

	/** @var string */
	private $namespace;

	/** @var string|int|\DateTime */
	private $expiration;


	public function __construct(IStorage $storage, ?string $namespace = NULL)
	{
		$this->storage = $storage;
		$this->namespace = $namespace . Cache::NAMESPACE_SEPARATOR;
	}


	public function getStorage(): IStorage
	{
		return $this->storage;
	}


	public function getNamespace(): string
	{
		return (string) substr($this->namespace, 0, -1);
	}


	/**
	 * @param \DateTime|int|string $expiration
	 * @return self
	 */
	public function setExpiration($expiration): self
	{
		$this->expiration = $expiration;
		return $this;
	}


	/**
	 * @inheritdoc
	 */
	public function get($key)
	{
		return $this->storage->read($this->generateKey($key));
	}


	/**
	 * @inheritdoc
	 */
	public function set($key, $data)
	{
		$key = $this->generateKey($key);

		if ($data === NULL) {
			$this->storage->remove($key);
		} else {
			$this->storage->write($key, $data, $this->getDependencies());
		}
	}


	/**
	 * @return mixed[]
	 */
	private function getDependencies(): array
	{
		$dependencies = [];

		if (isset($this->expiration)) {
			$dependencies[Cache::EXPIRATION] = Nette\Utils\DateTime::from($this->expiration)->format('U') - time();
		}

		return $dependencies;
	}


	/**
	 * Generates internal cache key.
	 */
	protected function generateKey(string $key): string
	{
		return $this->namespace . md5(is_scalar($key) ? $key : serialize($key));
	}


	/**
	 * Clears all cached items.
	 */
	public function clean(): void
	{
		$this->storage->clean([Cache::ALL => TRUE]);
	}

}
