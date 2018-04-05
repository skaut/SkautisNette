<?php

declare(strict_types=1);

namespace Skautis\Nette;

use Nette;
use Skautis\Config;
use Skautis\Wsdl\WebServiceFactory;
use Skautis\Wsdl\WsdlManager;
use Skautis\Nette\SessionAdapter;
use Skautis\User;
use Skautis\Skautis;
use Skautis\Nette\Tracy\Panel;
use Tracy\Debugger;


/**
 * Skautis extension for Nette Framework
 *
 * @author Hána František
 * @author Petr Morávek
 */
class SkautisExtension extends Nette\DI\CompilerExtension
{

	/** @var array */
	public $defaults = [
		'applicationId' => NULL,
		'testMode' => FALSE,
		'profiler' => NULL,
		'cache' => TRUE,
		'compression' => TRUE,
	];


	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults);
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
