<?php
namespace CentralApps\Base\ServiceProviders;

class TwigServiceProvider implements ServiceProviderInterface
{
	protected $bootPriority = 0;

	public function __construct($boot_priority=10, $key=null, $settings_prefix_key='settings')
	{
		$this->bootPriority = $boot_priority;
	}

	public function register(\CentralApps\Base\Application $application)
	{
		$container = $application->getContainer();
		$container[$this->key] = $container->share(function($c) {
			$loader = new \Twig_Loader_Filesystem('/path/to/templates');
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