<?php

/**
 * ApiGen 3.0dev - API documentation generator for PHP 5.3+
 *
 * Copyright (c) 2010-2011 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011-2012 Jaroslav Hanslík (https://github.com/kukulich)
 * Copyright (c) 2011-2012 Ondřej Nešpor (https://github.com/Andrewsville)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace ApiGen\Config;

use ApiGen\Config\Extension\ApiGenExtension;
use ApiGen\Config\Extension\ConfigExtension;
use ApiGen\Config\Extension\PluginsExtension;
use Nette\Caching\Storages\MemoryStorage;
use Nette\Config\Configurator as NetteConfigurator;
use Nette\Config\Compiler;
use Nette\Loaders\RobotLoader;
use Nette\Utils\LimitedScope;

/**
 * ApiGen configurator.
 *
 * Parses config files and creates internal configuration structures.
 */
class Configurator extends NetteConfigurator
{
	/**
	 * Config cache.
	 *
	 * @var \Nette\Caching\Storages\MemoryStorage
	 */
	private $cache;

	/**
	 * Autoloader.
	 *
	 * @var \Nette\Loaders\RobotLoader
	 */
	private $loader;

	/**
	 * CLI arguments.
	 *
	 * @var array
	 */
	private $arguments;

	/**
	 * Creates the configurator instance and prepares configuration cache.
	 */
	public function __construct(array $arguments)
	{
		parent::__construct();

		$this->cache = new MemoryStorage();

		$this->loader = new RobotLoader();
		$this->loader->setCacheStorage($this->cache);
		$this->loader->autoRebuild = false;

		$this->arguments = $arguments;
	}

	/**
	 * Returns the autoloader instance.
	 *
	 * @return \Nette\Loaders\RobotLoader
	 */
	public function createRobotLoader()
	{
		return $this->loader;
	}

	/**
	 * Returns the application DIC.
	 *
	 * @return \SystemContainer
	 */
	public function createContainer()
	{
		$c = $this->buildContainer();

		LimitedScope::evaluate($c);

		$container = new $this->parameters['container']['class'];
		$container->initialize();

		return $container;
	}

	/**
	 * Creates the DIC compiler.
	 *
	 * @return \Nette\Config\Compiler
	 */
	protected function createCompiler()
	{
		$compiler = new Compiler();
		$compiler->addExtension('config', new ConfigExtension($this->arguments));
		$compiler->addExtension('apigen', new ApiGenExtension());
		$compiler->addExtension('plugin', new PluginsExtension($this->createRobotLoader()));

		return $compiler;
	}
}
