<?php

declare(strict_types=1);

namespace Skaut\SkautisNette\Fixture;

use Skaut\Skautis\Wsdl\WebServiceInterface;

/**
 * Answers skautIS calls from JSON files instead of SOAP.
 *
 * {dir}/{Service}/{Method}.json holds the already normalised result (object, list of objects or null).
 * A variant for one argument, e.g. PersonAll__ID_Unit=1002.json, wins over the plain file; arguments
 * are tried in the order they were passed.
 */
final class FixtureWebService implements WebServiceInterface
{
    public function __construct(
        private readonly string $directory,
        private readonly string $service,
    ) {
    }

    public function call(string $functionName, array $arguments = []): mixed
    {
        $method = ucfirst($functionName);
        $args = isset($arguments[0]) && \is_array($arguments[0]) ? $arguments[0] : [];

        foreach ($args as $key => $value) {
            if (! \is_scalar($value)) {
                continue;
            }
            $variant = $this->path($method.'__'.$key.'='.(\is_bool($value) ? ($value ? 'true' : 'false') : (string) $value));
            if (is_file($variant)) {
                return $this->load($variant);
            }
        }

        $default = $this->path($method);
        if (! is_file($default)) {
            throw new FixtureNotFoundException(\sprintf('Missing skautIS fixture for %s.%s, create %s', $this->service, $method, $default));
        }

        return $this->load($default);
    }

    public function __call(string $functionName, array $arguments): mixed
    {
        return $this->call($functionName, $arguments);
    }

    private function path(string $name): string
    {
        return $this->directory.'/'.$this->service.'/'.$name.'.json';
    }

    private function load(string $file): mixed
    {
        return json_decode((string) file_get_contents($file), false, 512, \JSON_THROW_ON_ERROR);
    }
}
