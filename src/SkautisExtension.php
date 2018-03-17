<?php

declare(strict_types=1);

namespace Skautis\Nette;

use Nette;
use Nette\DI\Config;
use Skautis\Wsdl\WebServiceFactory;
use Skautis\Wsdl\WsdlManager;
use Skautis\Nette\SessionAdapter;
use Skautis\User;
use Skautis\Skautis;
use Skautis\Nette\Tracy\Panel;


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
		'profiler' => NULL,
		'cache' => TRUE,
		'compression' => TRUE,
	);


	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults);
		$config['profiler'] = isset($config['profiler']) ? $config['profiler'] : !empty($container->parameters['debugMode']);

		$container->addDefinition($this->prefix('config'))
			->setFactory(\Skautis\Config::class, array($config['applicationId'], $config['testMode'], $config['cache'], $config['compression']));

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

		if ($config['profiler'] && (class_exists('Tracy\Debugger') || class_exists('Nette\Diagnostics\Debugger'))) {
			$panel = $container->addDefinition($this->prefix('panel'))
				->setType(Panel::class);
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
