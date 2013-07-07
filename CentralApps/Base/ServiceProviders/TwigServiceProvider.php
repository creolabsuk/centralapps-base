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
			$settings = $c->getSettingFromNestedKey($nested_key = array($key));
			$cache_settings = $settings['cache'];
			$loader = new \Twig_Loader_Filesystem($settings['path']);
			$twig_config = array(
			    'cache' => (isset($cache_settings['enabled']) && true == $cache_settings['enabled']) ? ((isset($cache_settings['path'])) ? $cache_settings['path'] : null) : null,
			);
			if (1 == $settings['debug']) {
				$twig_config['debug'] = true;
			}
			$twig = new \Twig_Environment($loader, $twig_config);
			$twig->addExtension(new \Twig_Extension_Debug());

			return $twig;
		});

		$container['template_variables'] = $container->share(function($c){
			return new \CentralApps\Base\Views\TemplateVariables();
		});

		$application->registerInvokableFunction('render', function($template, $tags) use ($application, $key) {
			return $application->getContainer()[$key]->render($template, $tags);
		});

		$application->registerInvokableFunction('getTemplateEngineAdapter', function() use ($application, $key) {
			return new \CentralApps\Base\Views\TemplateEngineAdapters\TwigTemplateEngineAdapter($application->getContainer()[$key]);
		});

		$application->registerInvokableFunction('getView', function($view_name=null, $template_name=null, $variables=null) use ($application) {
			if (is_null($view_name)) {
				$view_class = "\CentralApps\Base\Views\BasicView";
			} else {
				$view_class = "\CentralApps\Base\Views\\" . $view_name;
			}
			$view = new $view_class();
		});
	}

	public function boot() {}

	public function getBootPriority()
	{
		return $this->bootPriority;
	}

}