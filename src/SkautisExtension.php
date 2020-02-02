<?php

namespace Skautis\Nette;

use Nette;
use Skautis;
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
		$config['profiler'] = isset($config['profiler']) ? $config['profiler'] : !empty($container->parameters['debugMode']);

		$container->addDefinition($this->prefix('config'))
			->setFactory(Skautis\Config::class, [$config['applicationId'], $config['testMode'], $config['cache'], $config['compression']]);

		$container->addDefinition($this->prefix('webServiceFactory'))
			->setFactory(Skautis\Wsdl\WebServiceFactory::class);

		$manager = $container->addDefinition($this->prefix('wsdlManager'))
			->setFactory(Skautis\Wsdl\WsdlManager::class);

		$container->addDefinition($this->prefix('session'))
			->setFactory(SessionAdapter::class);

		$container->addDefinition($this->prefix('user'))
			->setFactory(Skautis\User::class);

		$container->addDefinition($this->prefix('skautis'))
			->setFactory(Skautis\Skautis::class);

		if ($config['profiler'] && class_exists(Debugger::class)) {
			$panel = $container->addDefinition($this->prefix('panel'))
				->setFactory(Skautis\Nette\Tracy\Panel::class);
			$manager->addSetup([$panel, 'register'], [$manager]);
		}
	}

}
