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
		$settings = $container->getSettingFromNestedKey(array($key));
		$container[$this->key . '_provider_container'] = $container->share(function($c) use ($key, $settings) {

			$provider_container = new \CentralApps\Authentication\Providers\Container();

			$user_factory = $c->getFromNestedKey(explode(',', $settings['dependencies']['user_factory']));
			$user_gateway = $c->getFromNestedKey(explode(',', $settings['dependencies']['user_gateway']));

			if (true == $settings['providers']['username_password']['enabled']) {
				$username_password_provider = new \CentralApps\Authentication\Providers\UsernamePasswordProvider($c['request'], $user_factory, $user_gateway);
            	$username_password_provider->setUsernameField($settings['providers']['username_password']['username_field']);
            	$username_password_provider->setPasswordField($settings['providers']['username_password']['password_field']);
            	$provider_container->insert($username_password_provider, 0);
			}

			if (true == $settings['providers']['session']['enabled']) {
				$session_provider = new \CentralApps\Authentication\Providers\SessionProvider($c['request'], $user_factory, $user_gateway);
            	$session_provider->setSessionName($settings['providers']['session']['name']);
            	$provider_container->insert($session_provider, 10);
			}

			if (true == $settings['providers']['cookie']['enabled']) {
				$cookie_provider = new \CentralApps\Authentication\Providers\CookieProvider($c['request'], $user_factory, $user_gateway);
            	$cookie_provider->setCookieNames(explode(',', $settings['providers']['cookie']['names']));
            	$provider_container->insert($cookie_provider, 20);
			}

			return $provider_container;
		});

		$container[$this->key .'_settings'] = $container->share(function($c) use ($key, $settings){

			$user_factory = $c->getFromNestedKey(explode(',', $settings['dependencies']['user_factory']));
			$user_gateway = $c->getFromNestedKey(explode(',', $settings['dependencies']['user_gateway']));

			$authentication_container = array(
                'username_field' => $settings['providers']['username_password']['username_field'],
                'password_field' => $settings['providers']['username_password']['password_field'],
                'remember_password_field' => $settings['providers']['username_password']['remember_password_field'],
                'remember_password_yes_value' => $settings['providers']['username_password']['remember_password_yes_value'],
                'user_factory' => $user_factory,
                'user_gateway' => $user_gateway,
                'session_name' => $settings['providers']['session']['name'], // Shouldn't be setting this twice
                'cookie_names' => explode(',', $settings['providers']['cookie']['names']), // Shouldn't be setting this twice
                'session_processor' => null, //deprecated
                'cookie_processor' => null //deprecated
            );

            return new \CentralApps\Authentication\DependencyInjectionSettingsProvider($authentication_container);
		});

		$container[$this->key . '_processor'] = $container->share(function($c) use ($key, $settings) {
			return new \CentralApps\Authentication\Processor($c[$key . '_settings'], $c[$key . '_provider_container']);
		});

		$this->registerInvokableFunctions($application);
	}

	protected function registerInvokableFunctions($application)
	{
		$key = $this->key;
		$application->registerInvokableFunction('checkAuthentication', function() use ($key, $application){
			$container = $application->getContainer();
			$container[$key.'_processor']->checkForAuthentication();
			$container[$key.'_processor']->rememberPasswordIfRequested();
			$user = $container[$key.'_processor']->getUser();
			$container['current_user'] = (is_object($user)) ? $user : null;

			if ($container[$key.'_processor']->hasAttemptedToLogin()) {
				if (!is_null($container['current_user'])) {
					// went well
				} else {
					// went badly
					echo 'failed to login';
				}
			}
			/*
			oAuth only
			elseif ($container[$key.'_processor']->isAttemptingToRegister()) {
				$container[$key.'_processor']->handleRegister();
			}

			if (!is_null($container['current_user']) && $container[$key .'_processor']->isAttemptingToAttach()) {
	            if ($container[$key.'_processor']->handleAttach()) {
	                // attached
	            } else {
	                // attach fail
	            }
	        }*/
		});
	}

	public function boot()
	{
		//echo 'booting the authentication provder';
	}

	public function getBootPriority()
	{
		return $this->bootPriority;
	}
}