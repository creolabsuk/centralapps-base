<?php
namespace CentralApps\Base\ServiceProviders\Twig;

class TwigServiceProvider implements \CentralApps\Base\ServiceProviders\ServiceProviderInterface
{
	protected $bootPriority = 0;
	protected $key;

	public function __construct($boot_priority=10, $key=null, $settings_prefix_key='settings')
	{
		$this->bootPriority = $boot_priority;
		$this->key = (is_null($key)) ? 'twig' : $key;
	}

	public function register(\CentralApps\Base\Application $application)
	{
		$container = $application->getContainer();
		$container[$this->key] = $container->share(function($c) {
			$twig_loader = new \Twig_Loader_FileSystem('/');
			return new \Twig_Environment($loader);
		});
		$key = $this->key;
		$application->registerInvokableFunction('getTemplateEngineAdapter', function() use ($application, $key) {
			return new \CentralApps\Base\Views\TemplateEngineAdapters\TwigTemplateEngineAdapter($application->getContainer()[$key]);
		});

		$application->registerInvokableFunction('getView', function($view_name=null, $template_name=null, $variables=null) use ($application) {
			if (is_null($view_name)) {
				$view_class "\CentralApps\Base\Views\BasicView";
			} else {
				$view_class = "\CentralApps\Base\Views\\" . $view_name;
			}
			$view = new $view_class();
		});
	}

	public function boot()
	{

	}

	public function getBootPriority()
	{
		return $this->bootPriority;
	}
}