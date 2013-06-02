<?php
namespace CentralApps\Base\ServiceProviders;

class AuthenticationServiceProvider implements ServiceProviderInterface
{
	protected $bootPriority = 0;
	protected $key;

	public function __construct($boot_priority=10, $key=null)
	{
		$this->bootPriority = $boot_priority;
		$this->key = (is_null($key)) ? 'authentication' : $key;
	}

	public function register(\CentralApps\Base\Application $application)
	{
		$container = $application->getContainer();
		$container[$this->key] = $container->share(function($c) {
			echo 'a';
		});
	}

	public function boot()
	{
		echo 'booting the router';
	}

	public function getBootPriority()
	{

	}
}