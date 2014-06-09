<?php
namespace CentralApps\Base\ServiceProviders\Routing;

interface RouteFilter extends Filter
{
    public function filterRoute(array $route);
}
