<?php

namespace CentralApps\Base\Controllers;

abstract class AbstractController
{
    public function __construct(\CentralApps\Base\Containers\Container $container)
    {
        $this->container = $container;
    }
}
