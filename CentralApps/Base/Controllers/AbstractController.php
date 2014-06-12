<?php
namespace CentralApps\Base\Controllers;

abstract class AbstractController
{
    protected $container = null;

    public function __construct(\CentralApps\Base\Containers\Container $container)
    {
        $this->container = $container;
    }
    
    /**
     * Helper method for getting a response from the view
     * @param string $view the class of the view. Either fully qualified, or from within a View subnamespace one level up
     * @param mixed $model
     * @param string $model_name
     * @return mixed ideally a symfony HTTP response, but could be a string or something else
     */
    public function getResponse($view, $model = null, $model_name = null)
    {
        // Look to see if the view class name contains namespaces
        if (strpos($view, '\\') === false) {
            // No namespaces found, so we assume that the concrete controller class is in a sub-namespace at the same level
            // to the sub-namespace the view class is in. Extract the namespace, of the concrete controller
            // strip it back two levels (one for the class, one for the sub-name space)
            $ns = explode('\\', get_class($this));
            $count = count($ns);
            unset($ns[$count-1]);
            unset($ns[$count-2]);
            // then look in the views sub-namespace for the class named as $view
            $ns[] = 'Views';
            $ns[] = $view;
            $view = implode('\\', $ns);
        }
        
        // Instantiate the view with the container
        $view = new $view($this->container);

        // ideally, view->render should return a symfony HTTP response object or equivalent
        return $view->render($model, $model_name);
    }
}
