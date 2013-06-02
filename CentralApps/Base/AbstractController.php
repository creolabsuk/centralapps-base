<?php
namespace CentralApps\Base;

abstract class AbstractController
{
	protected $container;

	public function __construct($container)
	{
		$this->container = $container;
	}
}