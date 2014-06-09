<?php
namespace CentralApps\Base\Controllers;

abstract class AbstractController
{
    protected $container = null;

    public function __construct(\CentralApps\Base\Containers\Container $container)
    {
        $this->container = $container;
    }
}
