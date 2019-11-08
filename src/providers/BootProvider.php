<?php

namespace Hiraeth\Sentry;

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
	 * @var object $instance The unprepared instance of the object
	 * @param Application $app The application instance for which the provider operates
	 * @return object The prepared instance
	 */
	public function __invoke($instance, Hiraeth\Application $app): object
	{
		StreamWrapper::setFilesystemMap($app->get(FilesystemMap::class));
		StreamWrapper::register($app->getConfig('packages/gaufrette', 'scheme', 'vol'));

		return $instance;
	}
}
