<?php
namespace CentralApps\Base\ServiceProviders\Routing;

class NewRelicRouteNameFilter implements RouteFilter
{
    protected $routeNameProperty = '_route';

    public function setRouteNameProperty($route_name_property)
    {
        $this->routeNameProperty = $route_name_property;
    }

    public function filterRoute(array $route)
    {
        if (extension_loaded('newrelic')) {
            newrelic_name_transaction($route[$this->routeNameProperty]);
        }

        return $route;
    }
}
