<?php
namespace CentralApps\Base\ServiceProviders;

class AuthenticationServiceProvider implements ServiceProviderInterface
{
	protected $bootPriority = 0;
	protected $key;

	public function __construct($boot_priority=10, $key=null)
	{
		$this->bootPriority = $boot_priority;
		$this->key = (is_null($key)) ? 'authentication' : $key;
	}

	public function register(\CentralApps\Base\Application $application)
	{
		$container = $application->getContainer();
		$key = $this->key;
		$settings = $c->getSettingFromNestedKey(array($key));
		$container[$this->key . '_provider_container'] = $container->share(function($c) use ($key, $settings) {

			$provider_container = new AuthenticationProviderContainer();

			$user_factory = $c->getFromNestedKey(implode(',', $settings['dependencies']['user_factory']);
			$user_gateway = $c->getFromNestedKey(implode(',', $authentication_settings['dependencies']['user_gateway']);

			if (true == $settings['providers']['username_password']['enabled']) {
				$username_password_provider = new UsernamePasswordProvider($c['request'], $user_factory, $user_gateway);
            	$username_password_provider->setUsernameField($settings['providers']['username_password']['username_field']);
            	$provider_container->insert($username_password_provider, 0);
			}

			if (true == $settings['providers']['session']['enabled']) {
				$session_provider = new SessionProvider($c['request'], $user_factory, $user_gateway);
            	$session_provider->setSessionName(implode(',', $settings['providers']['session']['name']);
            	$provider_container->insert($session_provider, 10);
			}

			if (true == $settings['providers']['cookie']['enabled']) {
				$cookie_provider = new CookieProvider($c['request'], $user_factory, $user_gateway);
            	$cookie_provider->setCookieNames(implode(',', $settings['providers']['cookie']['names']);
            	$provider_container->insert($cookie_provider, 20);
			}

			return $provider_container;
		});

		$container[$this->key .'_settings'] = $container->share(function($c) use ($key, $settings){
			$user_factory = $c->getFromNestedKey(implode(',', $settings['dependencies']['user_factory']);
			$user_gateway = $c->getFromNestedKey(implode(',', $authentication_settings['dependencies']['user_gateway']);

			$authentication_container = array(
                'username_field' => $settings['providers']['username_password']['username_field'],
                'password_field' => 'password',
                'remember_password_field' => 'remember',
                'remember_password_yes_value' => '1',
                'user_factory' => $user_factory,
                'user_gateway' => $user_gateway,
                'session_name' => $settings['providers']['session']['name'], // Shouldn't be setting this twice
                'cookie_names' => implode(',', $settings['providers']['cookie']['names']), // Shouldn't be setting this twice
                'session_processor' => null, //deprecated
                'cookie_processor' => null //deprecated
            );
		});
	}

	protected function registerInvokableFunctions($application)
	{
		$application->registerInvokableFunction('checkAuthentication', function() use ($application){
			//
		});
	}

	public function boot()
	{
		echo 'booting the router';
	}

	public function getBootPriority()
	{

	}
}