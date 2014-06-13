<?php
namespace CentralApps\Base\Views;

trait StockViewImplementationTrait
{
    protected $template = null;
    protected $statusCode = 200;

    public function addTemplateVariable($variable_name, $variable)
    {
        // TODO: de-couple from the container if possible
        $this->container['template_variables'][$variable_name] = $variable;
    }

    protected function prepare()
    {
        $this->prepareApplicationView();
        $this->templateEngineAdapter->useVariables($this->container['template_variables']->getVariables());
    }

    public function generate($model = null, $model_name = null)
    {
        $this->prepare();
    }

    public function render($model = null, $model_name = null)
    {
        // TODO: de-couple from the container if possible
        $response = $this->container['response'];
        $response->setStatusCode($this->statusCode);
        if (isset($this->container['api_context']) && true == $this->container['api_context']) {
            $response->setContent(json_encode($model)); // TODO: maybe use the json response?
        } else {
            $model_name = (is_null($model_name)) ? 'model' : $model_name;
            $this->container['template_variables'][$model_name] = $model;

            $this->templateEngineAdapter->useTemplate($this->template);
            $this->prepare();

            $response->setContent($this->templateEngineAdapter->getOutput());
        }

        return $response;
    }
}
