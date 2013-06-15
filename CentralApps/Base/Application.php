<?php
namespace CentralApps\Base;

class Application
{
	protected $container;
	protected $invokableFunctions = array();
	protected $serviceProviders = array();
	protected $bootSequence = null;
	protected $applicationRootFolder = null;
	protected $configurationKey = 'settings';

	public function __construct(\CentralApps\Base\Containers\AbstractContainer $container = null, $application_root_folder=null)
	{
		$this->container = (is_null($container)) ? new \CentralApps\Base\Containers\Container() : $container;
		$this->bootSequence = new \splPriorityQueue();
		$this->applicationRootFolder = is_null($application_root_folder) ? __DIR__.'/' : $application_root_folder;
	}

	public function getApplicationRootFolder()
	{
		return $this->applicationRootFolder;
	}

	public function setConfigurationKey($key)
	{
		$this->configurationKey = $key;
	}

	public function loadConfiguration()
	{
		$xml_loader = new \GroundSix\Config\XmlLoader(new \GroundSix\Config\ConfigurationsCollection());
		call_user_func_array(array($xml_loader, 'loadFiles'), func_get_args());
		$merger = new \GroundSix\Config\Merger();
		$this->container[$this->configurationKey] = $merger->merge($xml_loader->getConfigurations());
	}

	public function getExecutionContext()
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
		$this->serviceProviders[] = $provider;
	}

	// This magic method allows support for service providers key method invokation
	public function __call($method, $args)
	{
		if (array_key_exists($method, $this->invokableFunctions)) {
			return call_user_func_array($this->invokableFunctions[$method], $args);
		} else {
			throw new \RuntimeException("Invokable method " . $method . " has not been registered with the application");
		}
	}
}