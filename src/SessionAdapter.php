<?php

namespace Skautis\Nette;

use Nette;
use Skautis\SessionAdapter\AdapterInterface;


/**
 * Nette session adapter for Skautis library
 */
class SessionAdapter implements AdapterInterface
{

	/** @var Nette\Http\SessionSection */
	protected $sessionSection;


	/**
	 * @param Nette\Http\Session $session
	 */
	public function __construct(Nette\Http\Session $session)
	{
		$this->sessionSection = $session->getSection(__CLASS__);
	}


	/**
	 * @inheritdoc
	 */
	public function set($name, $object)
	{
		$this->sessionSection->$name = $object;
	}


	/**
	 * @inheritdoc
	 */
	public function has($name)
	{
		return isset($this->sessionSection->$name);
	}


	/**
	 * @inheritdoc
	 */
	public function get($name)
	{
		return $this->sessionSection->$name;
	}

}
