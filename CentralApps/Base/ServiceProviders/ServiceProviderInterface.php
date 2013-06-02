<?php
namespace CentralApps\Base\ServiceProviders;

interface ServiceProviderInterface
{
	public function __construct($boot_priority=10, $key=null);

	public function register(\CentralApps\Base\Application $application);

	public function boot();

	public function getBootPriority();
}