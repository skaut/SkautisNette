<?php

namespace Skautis\Nette;

use Skautis\SessionAdapter\AdapterInterface;
use Nette\Http\Session;

/**
 * Adapter pro pouziti Nette Session ve Skautisu
 */
class SessionAdapter implements AdapterInterface
{

    /**
     * @var Nette\Http\SessionSection
     */
    protected $sessionSection;

    public function __construct(Session $session)
    {
        $this->sessionSection = $session->getSection("__" . __CLASS__);
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
