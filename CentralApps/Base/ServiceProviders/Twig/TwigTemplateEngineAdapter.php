<?php
namespace CentralApps\Base\ServiceProviders\Twig;

class TwigTemplateEngineAdapter implements \CentralApps\Base\Views\TemplateEngineInterface
{
	protected $templateEngine;
	protected $templateName;
	protected $variables = array();

	public function __construct($template_engine=null)
	{
		$this->templateEngine = $template_engine;
	}

	public function useTemplate($template_name)
	{
		$this->templateName = $template_name;
	}

	public function useVariables($variables)
	{
		$this->varibles = $variables;
	}

	public function render()
	{
		echo $this->getOutput();
		exit;
	}

	public function getOutput()
	{
		return $this->templateEngine->render($this->templateName, $this->variables);
	}
}