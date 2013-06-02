<?php
namespace CentralApps\Base\Views;

abstract class AbstractView
{
	protected $container;
	protected $applicationView;
	protected $templateEngineAdapter;
	protected $cssFiles = array();
	protected $javaScriptFiles = array();
	protected $title;
	protected $helpers = array();



	public function __construct($container, $template_engine_adapter = null, $application_view=null)
	{
		$this->container = $container;
		$this->applicationView = $application_view;
		if (is_null($template_engine_adapter)) {
			$this->templateEngineAdapter = $container['template_engine_adapter'];
		} else {
			$this->templateEngineAdapter = $template_engine_adapter;
		}
	}

	public function generate($model=null, $model_name=null);

	public function addHelper(ViewHelperInterface $helper)
	{
		$this->helpers[] = $helper;
	}

}