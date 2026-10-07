<?php

declare(strict_types=1);

namespace Skaut\SkautisNette;

use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Skaut\Skautis\Config;
use Skaut\Skautis\Skautis;
use Skaut\Skautis\User;
use Skaut\Skautis\Wsdl\WebService;
use Skaut\Skautis\Wsdl\WebServiceFactory;
use Skaut\Skautis\Wsdl\WebServiceFactoryInterface;
use Skaut\Skautis\Wsdl\WsdlManager;
use Skaut\SkautisNette\Fixture\FixtureWebServiceFactory;
use Skaut\SkautisNette\Tracy\Panel;
use Skaut\SkautisNette\Tracy\QueryLog;
use stdClass;
use Tracy\Bar;

/**
 * Registers the skaut/skautis services.
 *
 * Services (prefixed with the extension name): config, eventDispatcher, webServiceFactory, wsdlManager,
 * session, user, skautis and, with Tracy and the profiler on, queryLog and panel.
 */
class SkautisExtension extends CompilerExtension
{
    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'applicationId' => Expect::string()->required(),
            'testMode' => Expect::bool()->required(),
            'cache' => Expect::bool(Config::CACHE_ENABLED),
            'compression' => Expect::bool(Config::COMPRESSION_ENABLED),
            'profiler' => Expect::bool()->nullable(),
            'fixtures' => Expect::string()->nullable(),
        ]);
    }

    public function loadConfiguration(): void
    {
        $builder = $this->getContainerBuilder();
        /** @var stdClass $config */
        $config = $this->getConfig();

        $builder->addDefinition($this->prefix('config'))
            ->setFactory(Config::class, [$config->applicationId, $config->testMode, $config->cache, $config->compression]);

        // the library accepts one dispatcher per factory, so it gets ours and listeners subscribe to it
        $dispatcher = $builder->addDefinition($this->prefix('eventDispatcher'))
            ->setFactory(EventDispatcher::class);

        $factory = $builder->addDefinition($this->prefix('webServiceFactory'))
            ->setType(WebServiceFactoryInterface::class);
        if ($config->fixtures !== null) {
            $factory->setFactory(FixtureWebServiceFactory::class, [$config->fixtures]);
        } else {
            $factory->setFactory(WebServiceFactory::class, [WebService::class, $this->prefix('@eventDispatcher')]);
        }

        $builder->addDefinition($this->prefix('wsdlManager'))
            ->setFactory(WsdlManager::class);

        $builder->addDefinition($this->prefix('session'))
            ->setFactory(SessionAdapter::class);

        $builder->addDefinition($this->prefix('user'))
            ->setFactory(User::class);

        $builder->addDefinition($this->prefix('skautis'))
            ->setFactory(Skautis::class);

        $profiler = $config->profiler ?? ($builder->parameters['debugMode'] ?? false) === true;
        if ($profiler && class_exists(Bar::class)) {
            $builder->addDefinition($this->prefix('queryLog'))
                ->setFactory(QueryLog::class)
                ->setAutowired(false);
            $builder->addDefinition($this->prefix('panel'))
                ->setFactory(Panel::class, [$this->prefix('@queryLog')])
                ->setAutowired(false);
            $dispatcher->addSetup('addListener', [$this->prefix('@queryLog')]);
        }
    }

    public function afterCompile(ClassType $class): void
    {
        $builder = $this->getContainerBuilder();
        if (! $builder->hasDefinition($this->prefix('panel'))) {
            return;
        }

        // Tracy registered through its Nette extension (nette/bootstrap does that itself), or used standalone
        $bar = $builder->getByType(Bar::class);
        if ($bar !== null) {
            $this->initialization->addBody('$this->getService(?)->addPanel($this->getService(?));', [$bar, $this->prefix('panel')]);
        } else {
            $this->initialization->addBody('Tracy\Debugger::getBar()->addPanel($this->getService(?));', [$this->prefix('panel')]);
        }
    }
}
