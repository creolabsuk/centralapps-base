<?php
namespace CentralApps\Base;

class Application
{
	protected $container;
	protected $invokableFunctions = array();
	protected $bootSequence = null;

	public function __construct($container)
	{
		$this->container = $container;
		$this->bootSequence = new \splPriorityQueue();
	}

	public function getContext()
	{
		return (php_sapi_name() == 'cli') ? 'cli' : 'web';
	}

	public function boot()
	{
		$this->bootSequence->rewind();
		while ($this->bootSequence->valid()) {
			$boot_step = $this->bootSequence->current();
			$boot_step->boot();
			$this->bootSequence->next();
		}
	}

	// In PHP 5.4 we could use callable as the type hint for function
	public function registerInvokableFunction($key, $function)
	{
		$this->invokableFunctions[$key] = $function;
	}

	public function registerBootFunction($function)
	{
		$this->bootFunctions[] = $function;
	}

	public function getContainer()
	{
		return $this->container;
	}

	// Service providers are very inspired from Silex, however I've added
	// boot sequence support and explicit method invokation, this means that
	// providers boot() methods can be called in a developer defined order
	// and providers can have key methods registered with the application
	// key methods should be used sparingly, my recommendation would be for
	// when you _could_ use a boot sequence, but the call will alter the flow
	// of the application, e.g. route()
	public function registerServiceProvider(ServiceProviders\ServiceProviderInterface $provider)
	{
		$provider->register($this);
		$this->bootSequence->insert($provider, $provider->getBootPriority());
	}

	// This magic method allows support for service providers key method invokation
	public function __call($method, $args)
	{
		if (array_key_exists($method, $this->invokableFunctions)) {
			call_user_func($this->invokableFunctions[$method], $args);
		}
	}
}