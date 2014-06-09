<?php
namespace CentralApps\Base\ServiceProviders;

// TODO: it would be good to be able to add filters to the router
// so instead of callbacks, stripping tags and ignoring variables
// the filters would take care of that
class SymfonyRoutingServiceProvider implements ServiceProviderInterface
{
    protected $bootPriority = 0;
    protected $key;
    protected $filters = [];

    public function __construct($boot_priority=10, $key=null)
    {
        $this->bootPriority = $boot_priority;
        $this->key = (is_null($key)) ? 'router' : $key;
    }

    public function register(\CentralApps\Base\Application $application)
    {
        $container = $application->getContainer();
        $container[$this->key] = function ($c) use ($application) {
            $routing_settings = $c->getSettingFromNestedKey([$this->key]);
            $cache = (isset($routing_settings['cache'])) ? $routing_settings['cache'] : null;

            $locator = new \Symfony\Component\Config\FileLocator([$application->getApplicationRootFolder()]);
            $loader = new \Symfony\Component\Routing\Loader\YamlFileLoader($locator);
            $loader->load('routes.yml');

            $request = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
            $request_method = (isset($_POST) && isset($_POST['_method'])) ? $_POST['_method'] : (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '');
            $request_context = new \Symfony\Component\Routing\RequestContext(
                $request,
                $request_method,
                (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '')
            );

            $router = new \Symfony\Component\Routing\Router(new \Symfony\Component\Routing\Loader\YamlFileLoader($locator), 'routes.yml', ['cache_dir' => $cache], $request_context);

            return $router;
        };

        $container[$this->key . '.url_generator'] = function ($container) {
            return new \Symfony\Component\Routing\Generator\UrlGenerator($container[$this->key]->getRouteCollection(), new \Symfony\Component\Routing\RequestContext('', (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '')));
        };

        $this->registerRouteFunction($application);
    }

    public function addFilters(array $filters)
    {
        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }
    }

    public function addFilter(Routing\Filter $filter)
    {
        $this->filters[] = $filter;
    }

    // Made this a seperate method so logic can be injected for different projects
    public function registerRouteFunction($application)
    {
        $container = $application->getContainer();
        $application->registerInvokableFunction('route', function($url) use ($container) {

            foreach ($this->filters as $filter) {
                if ($filter instanceof \CentralApps\Base\ServiceProviders\Routing\UrlFilter) {
                    $url = $filter->filterUrl($url);
                }
            }

            try {
                $route = $container[$this->key]->match($url);
            } catch (\Exception $e) {
                throw  $e;
            }

            $route_action = [
                new $route['class']($container),
                $route['method']
            ];

            $variables = $route;

            foreach ($this->filters as $filter) {
                if ($filter instanceof \CentralApps\Base\ServiceProviders\Routing\RouteFilter) {
                    $variables = $filter->filterRoute($variables);
                }
            }

            try {
                return call_user_func_array($route_action, $variables);
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
