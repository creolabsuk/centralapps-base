<?php
namespace CentralApps\Base\ServiceProviders;

class PdoServiceProvider implements ServiceProviderInterface
{
	public function register(Application $application, $key, $settings_prefix_key='settings')
	{
		$container = $application->getContainer();
		$container[$key] = $container->share();
	}
}