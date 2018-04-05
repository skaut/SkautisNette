<?php

declare(strict_types=1);

namespace Skautis\Nette;

use Nette;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Skautis\Wsdl\Decorator\Cache\CacheInterface;


/**
 * Nette cache adapter for Skautis library
 */
class CacheAdapter implements CacheInterface
{

	use Nette\SmartObject;

	/** @var IStorage */
	private $storage;

	/** @var string */
	private $namespace;

	/** @var string|int|\DateTime */
	private $expiration;


	/**
	 * @param IStorage $storage
	 * @param string|null $namespace
	 */
	public function __construct(IStorage $storage, $namespace = NULL)
	{
		$this->storage = $storage;
		$this->namespace = $namespace . Cache::NAMESPACE_SEPARATOR;
	}


	/**
	 * Returns cache storage.
	 * @return IStorage
	 */
	public function getStorage()
	{
		return $this->storage;
	}


	/**
	 * Returns cache namespace.
	 * @return string
	 */
	public function getNamespace()
	{
		return (string) substr($this->namespace, 0, -1);
	}


	/**
	 * @param \DateTime|int|string $expiration
	 * @return self
	 */
	public function setExpiration($expiration)
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
	 * @return array
	 */
	private function getDependencies()
	{
		$dependencies = array();

		if (isset($this->expiration)) {
			$dependencies[Cache::EXPIRATION] = Nette\Utils\DateTime::from($this->expiration)->format('U') - time();
		}

		return $dependencies;
	}


	/**
	 * Generates internal cache key.
	 *
	 * @param string $key
	 * @return string
	 */
	protected function generateKey($key)
	{
		return $this->namespace . md5(is_scalar($key) ? $key : serialize($key));
	}


	/**
	 * Clears all cached items.
	 */
	public function clean()
	{
		$this->storage->clean(array(Cache::ALL => TRUE));
	}

}
