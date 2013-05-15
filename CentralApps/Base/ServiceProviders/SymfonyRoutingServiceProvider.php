<?php
namespace CentralApps\Base\ServiceProviders;

class SymfonyRoutingServiceProvider implements ServiceProviderInterface
{
	protected $bootPriority = 0;
	protected $key;

	public function __construct($boot_priority=10, $key=null, $settings_prefix_key='settings')
	{
		$this->bootPriority = $boot_priority;
		$this->key = (is_null($key)) ? 'router' : $key;
	}

	public function register(\CentralApps\Base\Application $application)
	{
		$container = $application->getContainer();
		$container[$this->key] = $container->share(function($c) {
			echo 'a';
		});

		$application->registerInvokableFunction('route', function() use ($application) {
			echo 'routing';
		});
	}

	public function boot()
	{
		echo 'booting the router';
	}

	public function getBootPriority()
	{
		return $this->bootPriority;
	}
}