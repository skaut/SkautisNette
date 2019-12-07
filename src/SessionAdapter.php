<?php

declare(strict_types=1);

namespace Skautis\Nette;

use Nette;
use Skautis\SessionAdapter\AdapterInterface;


class SessionAdapter implements AdapterInterface
{

	use Nette\SmartObject;

	/** @var Nette\Http\SessionSection */
	protected $sessionSection;


	public function __construct(Nette\Http\Session $session)
	{
		$this->sessionSection = $session->getSection(__CLASS__);
	}


	/**
	 * @inheritdoc
	 */
	public function set(string $name, $object): void
	{
		$this->sessionSection->$name = $object;
	}


	/**
	 * @inheritdoc
	 */
	public function has(string $name): bool
	{
		return isset($this->sessionSection->$name);
	}


	/**
	 * @inheritdoc
	 */
	public function get(string  $name)
	{
		return $this->sessionSection->$name;
	}

}
