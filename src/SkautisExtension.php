<?php

declare(strict_types=1);

namespace Skautis\Nette;

use Nette;
use Nette\Schema\Expect;
use Skautis\Config;
use Skautis\Wsdl\WebServiceFactory;
use Skautis\Wsdl\WsdlManager;
use Skautis\Nette\SessionAdapter;
use Skautis\User;
use Skautis\Skautis;
use Skautis\Nette\Tracy\Panel;
use Tracy\Debugger;


class SkautisExtension extends Nette\DI\CompilerExtension
{
	public function getConfigSchema() : Nette\Schema\Schema
	{
		return Expect::structure([
			'applicationId' => Expect::string()->required(),
			'testMode' => Expect::bool(false),
			'profiler' => Expect::bool()->nullable(),
			'cache' => Expect::bool(true),
			'compression' => Expect::bool(true),
		]);
	}

	public function loadConfiguration(): void
	{
		$container = $this->getContainerBuilder();
		$config = (array) $this->getConfig();

		$config['profiler'] = $config['profiler'] ?? !empty($container->parameters['debugMode']);

		$container->addDefinition($this->prefix('config'))
			->setFactory(Config::class, [$config['applicationId'], $config['testMode'], $config['cache'], $config['compression']]);

		$container->addDefinition($this->prefix('webServiceFactory'))
			->setType(WebServiceFactory::class);

		$manager = $container->addDefinition($this->prefix('wsdlManager'))
			->setType(WsdlManager::class);

		$container->addDefinition($this->prefix('session'))
			->setType(SessionAdapter::class);

		$container->addDefinition($this->prefix('user'))
			->setType(User::class);

		$container->addDefinition($this->prefix('skautis'))
			->setType(Skautis::class);

		if ($config['profiler'] && class_exists(Debugger::class)) {
			$panel = $container->addDefinition($this->prefix('panel'))
				->setType(Panel::class);
			$manager->addSetup([$panel, 'register'], [$manager]);
		}
	}

}
