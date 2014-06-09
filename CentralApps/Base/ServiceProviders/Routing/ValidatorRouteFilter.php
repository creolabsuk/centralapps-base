<?php
namespace CentralApps\Base\ServiceProviders\Routing;

class ValidatorRouteFilter implements RouteFilter
{
    protected $validator = null;

    public function setValidator($validator)
    {
        // TODO: type hinting on validator for the validateFromRoute method
        $this->validator = $validator;
    }

    public function filterRoute(array $route)
    {
        if (!is_null($this->validator)) {
            $this->validator->validateFromRoute($route);
        }

        return $route; // validateFromRoute alters route by reference magic
    }
}
