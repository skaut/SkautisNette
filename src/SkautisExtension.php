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
			->setClass(Skautis\Config::class, array($config['applicationId'], $config['testMode'], $config['cache'], $config['compression']));

		$container->addDefinition($this->prefix('webServiceFactory'))
			->setClass(Skautis\Wsdl\WebServiceFactory::class);

		$manager = $container->addDefinition($this->prefix('wsdlManager'))
			->setClass(Skautis\Wsdl\WsdlManager::class);

		$container->addDefinition($this->prefix('session'))
			->setClass(SessionAdapter::class);

		$container->addDefinition($this->prefix('user'))
			->setClass(Skautis\User::class);

		$container->addDefinition($this->prefix('skautis'))
			->setClass(Skautis\Skautis::class);

		if ($config['profiler'] && class_exists(Debugger::class)) {
			$panel = $container->addDefinition($this->prefix('panel'))
				->setClass(Skautis\Nette\Tracy\Panel::class);
			$manager->addSetup([$panel, 'register'], array($manager));
		}
	}

}
