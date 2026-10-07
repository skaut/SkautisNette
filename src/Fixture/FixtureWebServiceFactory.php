<?php

declare(strict_types=1);

namespace Skaut\SkautisNette\Fixture;

use Psr\EventDispatcher\EventDispatcherInterface;
use Skaut\Skautis\InvalidArgumentException;
use Skaut\Skautis\Wsdl\WebServiceFactoryInterface;
use Skaut\Skautis\Wsdl\WebServiceInterface;

/**
 * Creates fixture-backed web services; the service name is taken from the WSDL URL the library builds.
 */
final class FixtureWebServiceFactory implements WebServiceFactoryInterface
{
    public function __construct(private readonly string $directory)
    {
        if (! is_dir($directory)) {
            throw new InvalidArgumentException("skautIS fixture directory '$directory' does not exist.");
        }
    }

    public function createWebService(string $url, array $options): WebServiceInterface
    {
        if (preg_match('~/JunakWebservice/([A-Za-z]+)\.asmx~', $url, $matches) !== 1) {
            throw new InvalidArgumentException("Cannot read the web service name from '$url'.");
        }

        return new FixtureWebService($this->directory, $matches[1]);
    }

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        // fixtures do not dispatch events
    }
}
