<?php
namespace CentralApps\Base\Views;

class BasicView extends AbstractView
{
    protected function prepare($model = null, $model_name = null)
    {
        $this->prepareApplicationView();
        if (is_null($model_name)) {
            if (is_array($model)) {
                foreach ($model as $key => $value) {
                    $this->container['template_variables'][$key] = $value;
                }
            }
        } else {
            $this->container['template_variables'][$model_name] = $model;
        }

        $this->templateEngineAdapter->useVariables($this->container['template_variables']->getVariables());
    }

    public function generate($model = null, $model_name = null)
    {
        $this->prepare($model, $model_name);
        $this->templateEngineAdapter->useTemplate('base.html.twig');

        return $this->templateEngineAdapter->getOutput();
    }

    public function render($model = null, $model_name = null)
    {
        echo $this->generate($model, $model_name);
        exit;
    }

    public function renderWithTemplate($model = null, $model_name = null, $template_name = 'base.html.twig')
    {
        $this->prepare($model, $model_name);
        $this->templateEngineAdapter->useTemplate($template_name);
        echo $this->templateEngineAdapter->getOutput();
        exit;
    }
}
