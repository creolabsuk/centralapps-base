<?php
namespace CentralApps\Base\Traits;

trait ContainerAwareTrait
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }
}
