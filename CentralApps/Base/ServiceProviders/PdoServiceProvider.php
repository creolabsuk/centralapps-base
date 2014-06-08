<?php
namespace CentralApps\Base\ServiceProviders;

class PdoServiceProvider implements ServiceProviderInterface
{
	protected $bootPriority = 0;
	protected $key;
	protected $developmentMode = false;
	protected $type = 'mysql';

	public function __construct($boot_priority = 10, $key = null)
	{
		$this->bootPriority = $boot_priority;
		$this->key = (is_null($key)) ? 'pdo' : $key;
	}

	public function setDevelopmentMode($development_mode)
	{
		$this->developmentMode = $development_mode;
	}

	public function register(\CentralApps\Base\Application $application)
	{
		$container = $application->getContainer();

		$container[$this->key . '_' . $this->type] = function ($c) {
			$settings = $c->getSettingFromNestedKey(array('databases', $this->key, $this->type));
			try {
				$password = (is_array($settings['password'])) ? '' : $settings['password'];
				$class = (isset($settings['development_mode']) && true == $settings['development_mode']) ? '\CentralApps\Pdo\Pdo' : '\Pdo';
                $db = new $class("{$this->type}:host={$settings['host']};port={$settings['port']};dbname={$settings['database']}", $settings['user'], $password, array(\PDO::ATTR_PERSISTENT => true, \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
                $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                return $db;
            } catch (\Exception $e) {
                throw $e;
            }
		};
	}

	public function boot()
	{

	}

	public function getBootPriority()
	{
		return $this->bootPriority;
	}
}
