<?php
namespace CentralApps\Base\ServiceProviders\Twig\Extensions;

class Route extends \Twig_Extension
{
    protected $urlGenerator;
    protected $container = array();

    public function __construct(\Symfony\Component\Routing\Generator\UrlGenerator\Interface $url_generator, $container = [])
    {
        $this->urlGenerator = $url_generator;
        $this->container = $container;
    }

    public function route($route_name, $params = [])
    {
        $url = $this->urlGenerator->generate($route_name, $params);

        return $this->accountForExtraParams($url);
    }

    public function getFunctions()
    {
        return [
            'route'  => new \Twig_Function_Method($this, 'route'),
        ];
    }

    public function getName()
    {
        return 'route';
    }

    /**
     * Method to be overriden in child classes to add extra functionality
     * e.g. deal with pagination and stuff like that
     * @param string $url 
     * @return string
     */
    protected function accountForExtraParams($url)
    {
        return $url;
    }
}
