<?php

namespace Hiraeth\Gaufrette;

use Hiraeth;
use Gaufrette\StreamWrapper;
use Gaufrette\FilesystemMap;

/**
 *
 */
class BootProvider implements Hiraeth\Provider
{
	/**
	 * Get the interfaces for which the provider operates.
	 *
	 * @access public
	 * @return array A list of interfaces for which the provider operates
	 */
	static public function getInterfaces(): array
	{
		return [
			Hiraeth\Application::BOOT
		];
	}


	/**
	 * Prepare the instance.
	 *
	 * @access public
	 * @var object $state The application shared state
	 * @param Application $app The application instance for which the provider operates
	 * @return object The prepared instance
	 */
	public function __invoke($state, Hiraeth\Application $app): object
	{
		StreamWrapper::setFilesystemMap($app->get(FilesystemMap::class));
		StreamWrapper::register($app->getConfig('packages/gaufrette', 'gaufrette.scheme', 'vol'));

		return $state;
	}
}
