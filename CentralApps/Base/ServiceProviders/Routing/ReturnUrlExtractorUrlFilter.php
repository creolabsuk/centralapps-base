<?php
namespace CentralApps\Base\ServiceProviders\Routing;

class ReturnUrlExtractorUrlFilter implements UrlFilter
{
    protected $returnUrlKey = 'return_url';
    protected $returnUrl = null; // TODO: method of getting this from the application

    public function filterUrl($url)
    {
        $return_url = [];
        preg_match('/(\?|\&)?' . $this->returnUrlKey . '=[^\&]+/', $url, $return_url);
        if (count($return_url) > 0) {
            $this->returnUrl = preg_replace('/(\?|\&)?' . $this->returnUrlKey . '=/', '', $return_url[0]);
        }

        $url = preg_replace('/(\?|\&)?' . $this->returnUrlKey . '=[^\&]+/', '', $url);

        return $url;
    }
}
