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

	public function setApplicationView(ApplicationViewInterface $application_view)
	{
		$this->applicationView = $application_view;
	}

	public function prepareApplicationView()
	{
		if (!is_null($this->applicationView)) {
			$this->applicationView->preParseHook($this->templateEngineAdapter);
		}
	}

	abstract public function generate($model=null, $model_name=null);

	abstract public function render($model=null, $model_name=null);

	public function addHelper(ViewHelperInterface $helper)
	{
		$this->helpers[] = $helper;
	}

}
