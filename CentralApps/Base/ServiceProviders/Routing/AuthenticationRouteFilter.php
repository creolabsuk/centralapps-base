<?php
namespace CentralApps\Base\ServiceProviders\Routing;

class AuthenticationRouteFilter implements RouteFilter
{
    protected $container = [];
    protected $currentUser = null;
    protected $loggedInKey = 'logged_in';
    protected $notLoggedInException = '\Exception';

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function setLoggedInException($exception_class)
    {
        $this->notLoggedInException = $exception_class;
    }

    public function filterRoute(array $route)
    {
        if (isset($route[$this->loggedInKey]) && true == $route[$this->loggedInKey]) {
            if (is_null($this->container['current_user'])) {
                $exception_class = $this->notLoggedInException;
                throw new $exception_class('You need to be logged in to view this page');
            }

            // TODO: support for permissions via a can() interface
        }

        return $route;
    }
}
