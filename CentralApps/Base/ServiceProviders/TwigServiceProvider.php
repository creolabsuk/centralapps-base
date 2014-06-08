<?php
namespace CentralApps\Base\ServiceProviders;

class TwigServiceProvider implements ServiceProviderInterface
{
    protected $bootPriority = 0;
    protected $key;

    public function __construct($boot_priority = 10, $key = null)
    {
        $this->bootPriority = $boot_priority;
        $this->key = (is_null($key)) ? 'twig' : $key;
    }

    public function register(\CentralApps\Base\Application $application)
    {
        $container = $application->getContainer();

        $container[$this->key] = function ($c) {
            $settings = $c->getSettingFromNestedKey($nested_key = [$this->key]);
            $cache_settings = $settings['cache'];
            $loader = new \Twig_Loader_Filesystem($settings['path']);
            $twig_config = [
                'cache' => (isset($cache_settings['enabled']) && true == $cache_settings['enabled']) ? ((isset($cache_settings['path'])) ? $cache_settings['path'] : null) : null,
            ];
            if (1 == $settings['debug']) {
                $twig_config['debug'] = true;
            }
            $twig = new \Twig_Environment($loader, $twig_config);
            $twig->addExtension(new \Twig_Extension_Debug());

            return $twig;
        };

        $container['template_variables'] = function ($c){
            return new \CentralApps\Base\Views\TemplateVariables();
        };

        $application->registerInvokableFunction('render', function ($template, $tags) use ($application) {
            return $application->getContainer()[$this->key]->render($template, $tags);
        });

        $application->registerInvokableFunction('getTemplateEngineAdapter', function() use ($application) {
            return new \CentralApps\Base\ServiceProviders\Twig\TwigTemplateEngineAdapter($application->getContainer()[$this->key]);
        });

        $container['template_engine_adapter'] = function ($c) use ($application) {
            return $application->getTemplateEngineAdapter();
        };

        $application->registerInvokableFunction('getView', function($view_name = null, $template_name = null, $variables = null) use ($application) {
            if (is_null($view_name)) {
                $view_class = "\CentralApps\Base\Views\BasicView";
            } else {
                $view_class = "\CentralApps\Base\Views\\" . $view_name;
            }
            $view = new $view_class();
        });
    }

    public function boot() {}

    public function getBootPriority()
    {
        return $this->bootPriority;
    }
}
