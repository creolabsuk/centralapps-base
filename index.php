<?php
require_once(__DIR__.'/vendor/autoload.php');
require_once(__DIR__.'/bootstrap.php');

$application = new \CentralApps\Base\Application(new \Pimple\Container());

if ('web' === $application->getContext()) {
	// register web context related service providers
	$application->registerServiceProvider(new \CentralApps\Base\ServiceProviders\AuthenticationServiceProvider());
} else {
	// register cli contet related service providers
}
// register any shared service providers
$application->registerServiceProvider(new \CentralApps\Base\ServiceProviders\SymfonyRoutingServiceProvider());

// allow any service providers to boot
$application->boot();

// special method invokation of the routing service providers route method
$application->route();
