<?php
namespace CentralApps\Base\ServiceProviders;

class SymfonyRoutingServiceProvider implements ServiceProviderInterface
{
	protected $bootPriority = 0;
	protected $key;

	public function __construct($boot_priority=10, $key=null)
	{
		$this->bootPriority = $boot_priority;
		$this->key = (is_null($key)) ? 'router' : $key;
	}

	public function register(\CentralApps\Base\Application $application)
	{
		$key = $this->key;
		$container = $application->getContainer();
		$container[$this->key] = $container->share(function($c) use ($key, $application) {
			// routing settings
			$routing_settings = $c->getSettingFromNestedKey($nested_key = array($key));
			$cache = (isset($routing_settings['cache'])) ? $routing_settings['cache'] : null;

			$locator = new \Symfony\Component\Config\FileLocator(array($application->getApplicationRootFolder()));
	        $loader = new \Symfony\Component\Routing\Loader\YamlFileLoader($locator);
	        $loader->load('routes.yml');
	        $request = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
	        $request_method = (isset($_POST) && isset($_POST['_method'])) ? $_POST['_method'] : $_SERVER['REQUEST_METHOD'];
	        $requestContext = new \Symfony\Component\Routing\RequestContext($request, $request_method);
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
		$application->registerInvokableFunction('route', function($url=null, $remove_utm_tags=true, $variables_to_ignore = array(), $pre_processing_callback = null) use ($application, $key) {
			$url = is_null($url) ? $_SERVER['REQUEST_URI'] : $url;
			if ($remove_utm_tags) {
				$url = preg_replace('/(\?|\&)?utm_[a-z]+=[^\&]+/', '', $url);
				$url = (strlen($url) > 1) ? rtrim($url, '/') : $url;
			}
			$container = $application->getContainer();
			$router = $container[$key];
			try {
				$route = $container[$key]->match($url);
	            $controller = new $route['class']($application->getContainer());
	            $variables = $route;
	            $variables_to_ignore = array_merge($variables_to_ignore, array('name', 'class', 'method', '_route'));
	            foreach ($variables_to_ignore as $ignore) {
	            	if (isset($variables[$ignore])) {
	            		unset($variables[$ignore]);
	            	}
	            }

	            if (!is_null($pre_processing_callback)) {
	            	$pre_processing_callback($route);
	            }

	            call_user_func_array(array($controller, $route['method']), $variables);
	            //$action = $controller->$route['method']($variables);
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