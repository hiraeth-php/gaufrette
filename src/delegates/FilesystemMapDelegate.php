<?php

namespace Hiraeth\Gaufrette;

use Hiraeth;
use Gaufrette;
use League;

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
	 * {@inheritDoc}
	 */
	public static function getClass(): string
	{
		return Gaufrette\FilesystemMap::class;
	}


	/**
	 * {@inheritDoc}
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

			$adapter = $app->get($config['class'], $options);

			if ($adapter instanceof League\Flysystem\AdapterInterface) {
				$adapter = new Gaufrette\Adapter\Flysystem($adapter);
			}

			if ($app->getEnvironment('CACHING', TRUE) && !empty($config['caching']['ttl'])) {
				$adapter = new Gaufrette\Adapter\Cache($adapter, new Gaufrette\Adapter\Local(
					$config['caching']['path']   ?? $app->getDirectory(static::CACHE_PATH . $name),
					$config['caching']['create'] ?? TRUE,
					$config['caching']['mode']   ?? 0777
				),  $config['caching']['ttl']);
			}

			$map->set($name, new Gaufrette\Filesystem($adapter));
		}

		return $app->share($map);
	}
}
