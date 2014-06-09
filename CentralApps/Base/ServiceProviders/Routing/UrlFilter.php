<?php
namespace CentralApps\Base\ServiceProviders\Routing;

interface UrlFilter extends Filter
{
    public function filterUrl($url);
}
