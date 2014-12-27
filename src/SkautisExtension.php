<?php

namespace Skautis\Nette;

use Nette;


/**
 * Skautis extension for Nette Framework
 *
 * @author Hána František
 * @author Petr Morávek
 */
class SkautisExtension extends Nette\DI\CompilerExtension
{

	public $defaults = array(
		'applicationId' => NULL,
		'testMode' => FALSE,
		'profiler' => '%debugMode%',
		'cache' => TRUE,
		'compression' => TRUE,
	);


	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);
		$this->validateConfig($this->defaults, $config);

		$container->addDefinition($this->prefix('config'))
			->setClass('Skautis\Config', array($config['applicationId'], $config['testMode'], $config['cache'], $config['compression']));

		$container->addDefinition($this->prefix('webServiceFactory'))
			->setClass('Skautis\Wsdl\WebServiceFactory');

		$manager = $container->addDefinition($this->prefix('wsdlManager'))
			->setClass('Skautis\Wsdl\WsdlManager');

		$container->addDefinition($this->prefix('session'))
			->setClass('Skautis\Nette\SessionAdapter');

		$container->addDefinition($this->prefix('user'))
			->setClass('Skautis\User');

		$container->addDefinition($this->prefix('skautis'))
			->setClass('Skautis\Skautis');

		if ($config['profiler'] && (class_exists('Tracy\Debugger') || class_exists('Nette\Diagnostics\Debugger'))) {
			$panel = $container->addDefinition($this->prefix('panel'))
				->setClass('Skautis\Nette\Tracy\Panel');
			$manager->addSetup(array($panel, 'register'), array($manager));
		}
	}


	/**
	 * Checks whether $config contains only $expected items.
	 * @param array $expected configuration keys
	 * @param array|NULL $config to validate
	 * @param string|NULL $name configuration section name
	 * @throws Nette\InvalidStateException
	 */
	protected function validateConfig(array $expected, array $config = NULL, $name = NULL)
	{
		if ($extra = array_diff_key(func_num_args() > 1 ? (array) $config : $this->config, $expected)) {
			$name = $name ?: $this->name;
			$extra = implode(", $name.", array_keys($extra));
			throw new Nette\InvalidStateException("Unknown configuration option $name.$extra.");
		}
	}

}
