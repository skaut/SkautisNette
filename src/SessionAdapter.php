<?php

declare(strict_types=1);

namespace Skaut\SkautisNette;

use Nette\Http\Session;
use Nette\Http\SessionSection;
use Skaut\Skautis\SessionAdapter\AdapterInterface;

/**
 * Keeps the skautIS login data in a section of the Nette session.
 */
class SessionAdapter implements AdapterInterface
{
    private SessionSection $section;

    public function __construct(Session $session)
    {
        $this->section = $session->getSection(self::class);
    }

    public function set(string $name, mixed $object): void
    {
        $this->section->set($name, $object);
    }

    public function has(string $name): bool
    {
        return $this->section->get($name) !== null;
    }

    public function get(string $name): mixed
    {
        return $this->section->get($name);
    }
}
