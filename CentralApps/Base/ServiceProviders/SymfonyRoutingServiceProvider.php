<?php
namespace CentralApps\Base\ServiceProviders;

class SymfonyRoutingServiceProvider implements ServiceProviderInterface
{
	protected $bootPriority = 0;
	protected $key;
	protected $settingsPrefixKey;

	public function __construct($boot_priority=10, $key=null, $settings_prefix_key='settings')
	{
		$this->bootPriority = $boot_priority;
		$this->key = (is_null($key)) ? 'router' : $key;
		$this->settingsPrefixKey = $settings_prefix_key;
	}

	public function register(\CentralApps\Base\Application $application)
	{
		$key = $this->key;
		$container = $application->getContainer();
		$settings_key = $this->settingsPrefixKey;
		$container[$this->key] = $container->share(function($c) use ($settings_key) {
			$cache = null;
			$locator = new \Symfony\Component\Config\FileLocator(array( $c[$settings_key]['root_path'] ) );
	        $loader = new \Symfony\Component\Routing\Loader\YamlFileLoader($locator);
	        $loader->load('routes.yml');
	        $request = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
	        $requestContext = new \Symfony\Component\Routing\RequestContext($request, $_SERVER['REQUEST_METHOD']);
	        $router = new \Symfony\Component\Routing\Router(new \Symfony\Component\Routing\Loader\YamlFileLoader($locator), 'routes.yml', array('cache_dir' => $cache), $requestContext);
	        return $router;
		});

		$container[$this->key . '.url_generator'] = $container->share(function ($container) use ($key) {
            return new \Symfony\Component\Routing\Generator\UrlGenerator($container[$key]->getRouteCollection(), new \Symfony\Component\Routing\RequestContext('', $_SERVER['REQUEST_METHOD']));
        });

		$this->registerRouteFunction($application);
	}

	// Made this a seperate method so logic can be injected for different projects
	public function registerRouteFunction($application)
	{
		$key = $this->key;
		$application->registerInvokableFunction('route', function($url=null) use ($application, $key) {
			$url = is_null($url) ? $_SERVER['REQUEST_URI'] : $url;
			$container = $application->getContainer();
			$router = $container[$key];
			try {
				$route = $container[$key]->match($url);
	            $controller = new $route['class']($this->container);
	            $variables = $route;
	            unset($variables['name']);
	            unset($variables['class']);
	            unset($variables['method']);
	            $action = $controller->$route['method']($variables);
			} catch (\Exception $e) {
				throw $e;
			}
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