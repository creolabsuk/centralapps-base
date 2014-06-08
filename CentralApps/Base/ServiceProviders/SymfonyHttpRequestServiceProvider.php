<?php
namespace CentralApps\Base\ServiceProviders;

class SymfonyHttpRequestServiceProvider implements ServiceProviderInterface
{
    protected $bootPriority = 0;
    protected $key;

    public function __construct($boot_priority = 10, $key = null)
    {
        $this->bootPriority = $boot_priority;
        $this->key = (is_null($key)) ? 'request' : $key;
    }

    public function register(\CentralApps\Base\Application $application)
    {
        $container = $application->getContainer();
        $container[$this->key] = function ($c) {
            return \Symfony\Component\HttpFoundation\Request::createFromGlobals();
        };
    }

    public function boot()
    {

    }

    public function getBootPriority()
    {
        return $this->bootPriority;
    }
}
