<?php

namespace Hiraeth\Gaufrette;

use Hiraeth;
use Gaufrette\StreamWrapper;
use Gaufrette\FilesystemMap;

/**
 *
 */
class ApplicationProvider implements Hiraeth\Provider
{
	/**
	 * {@inheritDoc}
	 */
	static public function getInterfaces(): array
	{
		return [
			Hiraeth\Application::class
		];
	}


	/**
	 * {@inheritDoc}
	 */
	public function __invoke($state, Hiraeth\Application $app): object
	{
		StreamWrapper::setFilesystemMap($app->get(FilesystemMap::class));
		StreamWrapper::register($app->getConfig('packages/gaufrette', 'gaufrette.scheme', 'vol'));

		return $state;
	}
}
