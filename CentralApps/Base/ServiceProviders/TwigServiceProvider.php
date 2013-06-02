<?php
namespace CentralApps\Base\ServiceProviders;

class TwigServiceProvider implements ServiceProviderInterface
{
	protected $bootPriority = 0;
	protected $key;

	public function __construct($boot_priority=10, $key=null)
	{
		$this->bootPriority = $boot_priority;
		$this->key = (is_null($key)) ? 'twig' : $key;
	}

	public function register(\CentralApps\Base\Application $application)
	{
		$container = $application->getContainer();
		$key = $this->key;
		$container[$this->key] = $container->share(function($c) use ($key) {
			$settings = $c->getSettingFromNestedKey($key);
			$loader = new \Twig_Loader_Filesystem($settings['path']);
			$twig = new \Twig_Environment($loader, array(
			    'cache' => '/path/to/compilation_cache',
			));
			return $twig;
		});

		$application->registerInvokableFunction('render', function($template, $tags) use ($application, $key) {
			return $application->getContainer()[$key]->render($template, $tags);
		});
	}

	public function boot() {}

	public function getBootPriority()
	{
		return $this->bootPriority;
	}

}