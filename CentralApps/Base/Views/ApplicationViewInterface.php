<?php
namespace CentralApps\Base\Views;
/**
 * Application view interface
 * - used to contain application wide business logic
 */
interface ApplicationViewInterface
{
	public function preParseHook($container, TemplateEngineInterface $template_engine_adapter);
}