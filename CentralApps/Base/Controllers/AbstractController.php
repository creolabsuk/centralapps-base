<?php
namespace CentralApps\Base\Controllers;

abstract class AbstractController
{
    use \CentralApps\Base\Traits\Dispatcher;
    
    protected $container = null;
    protected $model;
    protected $formHelper = null;
    protected $formHelperPrep = null;
    protected $request;
    protected $requestArray = null;

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
    
    protected function populateRequest()
    {
        $this->request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
    }

    protected function get($request_key)
    {
        if (is_null($this->requestArray)) {
            $this->getRequestArrayToValidate();
        }

        return isset($this->requestArray[$request_key]) ? $this->requestArray[$request_key] : '';
    }

    protected function getRequestArrayToValidate()
    {
        if (is_null($this->requestArray)) {
            $this->requestArray = array_merge($this->request->request->all(), $this->request->files->all());
        }

        return $this->requestArray;
    }

    protected function getRequest()
    {
        return new \Symfony\Component\HttpFoundation\ParameterBag($this->getRequestArrayToValidate());
    }

    protected function validate($rules, $callback_method, $callback_params = array())
    {
        $data = $this->getRequestArrayToValidate();
        $violations = $this->container['validation_rules'][$rules]->getViolations($data);
        if (count($violations) > 0) {
            if (!is_null($this->formHelperPrep)) {
                $data = array_merge($data, $this->formHelperPrep);
            }
            $this->container['dispatcher']->dispatch('validation.failed', $this->container['standard_event']($violations));
            $this->formHelper = $this->container['form_helper_post']($data, $violations);
            
            return call_user_func_array(array($this, $callback_method), $callback_params);
        }
    }

    protected function prepareFormHelper($object = null, $id_getter = 'getId', $id_field = 'id')
    {
        if (is_null($this->formHelper) && !is_null($object)) {
            $this->formHelper = $this->container['form_helper_methods']($object);
        }

        if (!is_null($object)) {
            $this->formHelperPrep = array($id_field => $object->$id_getter());
        }

        return $this->formHelper;
    }
}
