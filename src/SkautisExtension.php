<?php

namespace Skautis\Nette;

use Nette;
use Nette\DI\Config;


/**
 * Skautis extension for Nette Framework
 *
 * @author Hána František
 * @author Petr Morávek
 */
class SkautisExtension extends Nette\DI\CompilerExtension
{

	/** @var array */
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
		$config = $this->validateConfig($this->defaults);

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
	 * BC with nette/di <2.3
	 * @param string $name
	 * @param array $args
	 * @return mixed
	 */
	public function __call($name, $args)
	{
		if ($name === 'validateConfig') {
			return call_user_func_array(array($this, '_validateConfig'), $args);
		}
		return parent::__call($name, $args);
	}


	/**
	 * Checks whether $config contains only $expected items and returns combined array.
	 * BC with nette/di <2.3
	 * @param array $expected configuration keys
	 * @param array|NULL $config to validate
	 * @param string|NULL $name configuration section name
	 * @return array
	 * @throws Nette\InvalidStateException
	 */
	private function _validateConfig(array $expected, array $config = NULL, $name = NULL)
	{
		if (func_num_args() === 1) {
			$config = $this->config;
		}
		if ($extra = array_diff_key((array) $config, $expected)) {
			$name = $name ?: $this->name;
			$extra = implode(", $name.", array_keys($extra));
			throw new Nette\InvalidStateException("Unknown configuration option $name.$extra.");
		}
		return Config\Helpers::merge($config, $expected);
	}

}
