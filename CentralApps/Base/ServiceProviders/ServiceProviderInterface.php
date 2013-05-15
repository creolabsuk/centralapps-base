<?php
namespace CentralApps\Base\ServiceProviders;

interface ServiceProviderInterface
{
	public function __construct($boot_priority=10, $key=null, $settings_prefix_key='settings');

	public function register(\CentralApps\Base\Application $application);

	public function boot();

	public function getBootPriority();
}