<?php

namespace Hiraeth\Gaufrette;

use Hiraeth;
use Gaufrette;

/**
 * Delegates are responsible for constructing dependencies for the dependency injector.
 *
 * Each delegate operates on a single concrete class and provides the class that it is capable
 * of building so that it can be registered easily with the application.
 */
class FilesystemMapDelegate implements Hiraeth\Delegate
{
	/**
	 *
	 */
	const CACHE_PATH = 'storage/cache/volumes/';


	/**
	 * Get the class for which the delegate operates.
	 *
	 * @static
	 * @access public
	 * @return string The class for which the delegate operates
	 */
	public static function getClass(): string
	{
		return Gaufrette\FilesystemMap::class;
	}


	/**
	 * Get the instance of the class for which the delegate operates.
	 *
	 * @access public
	 * @param Hiraeth\Application $app The application instance for which the delegate operates
	 * @return object The instance of the class for which the delegate operates
	 */
	public function __invoke(Hiraeth\Application $app): object
	{
		$map = new Gaufrette\FilesystemMap();

		foreach ($app->getConfig('*', 'volume', []) as $path => $config) {
			$name    = basename($path, '.jin');
			$options = array();

			if (!$config || $config['disabled'] ?? FALSE) {
				continue;
			}

			foreach ($app->getConfig($path, 'volume.options', []) as $key => $value) {
				$options[':' . $key] = $value;
			}

			switch($config['class']) {
				case Gaufrette\Adapter\Local::class:
					$options[':directory'] = $app->getDirectory($options[':directory']);
					break;
			}

			$adapter = $app->get($config['class'], $options);

			if ($adapter instanceof League\Flysystem\AdapterInterface) {
				$adapter = new Gaufrette\Adapter\Flysystem($adapter);
			}

			if ($app->getEnvironment('CACHING', TRUE) && !empty($config['caching']['ttl'])) {
				$adapter = new Gaufrette\Adapter\Cache($adapter, new Gaufrette\Adapter\Local(
					$app->getDirectory($config['caching']['path'] ?? static::CACHE_PATH . $name),
					$config['caching']['create'] ?? TRUE,
					$config['caching']['mode'] ?? 0777
				),  $config['caching']['ttl']);
			}

			$map->set($name, new Gaufrette\Filesystem($adapter));
		}

		return $app->share($map);
	}
}
