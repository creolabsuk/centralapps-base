<?php
namespace CentralApps\Base\ServiceProviders\Routing;

class RouteCleanupFilter implements RouteFilter
{
    protected $variablesToIgnore = [];

    public function setVariablesToIgnore(array $variables_to_ignore)
    {
        $this->variablesToIgnore = $variables_to_ignore;
    }

    public function filterRoute(array $route)
    {
        foreach ($this->variablesToIgnore as $ignore) {
            if (isset($variables[$ignore])) {
                unset($variables[$ignore]);
            }
        }

        return $variables;
    }
}
