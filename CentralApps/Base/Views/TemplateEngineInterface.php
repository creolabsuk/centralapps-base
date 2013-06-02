<?php
namespace CentralApps\Base\Views;

interface TemplateEngineInterface
{
	public function __construct($template_engine=null);
	public function useTemplate($template_name);
	public function useVariables($variables);
	public function render();
	public function getOutput();
}