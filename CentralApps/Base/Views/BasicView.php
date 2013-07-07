<?php
namespace CentralApps\Base\Views;

class BasicView extends AbstractView
{

	protected function prepare($model=null, $model_name=null)
	{
		$this->prepareApplicationView();
		$model_name = (is_null($model_name)) ? 'model' : $model_name;
		$vars = $this->container['template_variables']->getVariables();
		$this->templateEngineAdapter->useVariables(array_merge($this->container['template_variables']->getVariables(), array($model_name => $model)));
	}

	public function generate($model=null, $model_name=null)
	{
		$this->prepare($model, $model_name);
		$this->templateEngineAdapter->useTemplate('base.html.twig');
		return $this->templateEngineAdapter->getOutput();
	}

	public function render($model=null, $model_name=null)
	{
		echo $this->generate($model, $model_name);
		exit;
	}

	public function renderWithTemplate($model=null, $model_name=null, $template_name='base.html.twig')
	{
		$this->prepare($model, $model_name);
		$this->templateEngineAdapter->useTemplate($template_name);
		echo $this->templateEngineAdapter->getOutput();
		exit;
	}
}
