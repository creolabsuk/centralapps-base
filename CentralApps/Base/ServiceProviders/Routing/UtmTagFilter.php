<?php
namespace CentralApps\Base\ServiceProviders\Routing;

class UtmTagFilter implements UrlFilter
{
    public function filterUrl($url)
    {
        $url = preg_replace('/(\?|\&)?utm_[a-z]+=[^\&]+/', '', $url);
        $url = (strlen($url) > 1) ? rtrim($url, '/') : $url;

        return $url;
    }
}
