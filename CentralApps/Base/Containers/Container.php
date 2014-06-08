<?php
namespace CentralApps\Base\Containers;

class Container extends \Pimple\Container
{
    public function __construct (array $values = array())
    {
        parent::__construct($values);
        $this->addRequestToContainer();
    }

    protected function addRequestToContainer()
    {
        $request = array();
        $request['get'] = $_GET;
        $request['post'] = $_POST;
        $request['session'] = isset($_SESSION) ? $_SESSION : array();
        $request['cookies'] = $_COOKIE;
        $request['server'] = $_SERVER;
        $this['request'] = $request;
    }

    /**
     * Get a setting value which is deeply nested in the settings array, based on a structured array as the key
     * @param array $nested_key an array indicating the structure and depth of the setting in the settings array
     * @param String $settings_key the container key for the settings array
     * @param array $partially_processed_array if we are recursively looping, this will contain a subset of the settings array
     * @return mixed
     */
    public function getSettingFromNestedKey($nested_key = array(), $settings_key = 'settings')
    {
        array_unshift($nested_key, $settings_key);

        return $this->getFromNestedKey($nested_key);
    }

    /**
     * Get a value which is deeply nested in the container, based on a structured array as the key
     * @param array $nested_key an array indicating the structure and depth of the setting in the settings array
     * @param array $partially_processed_array if we are recursively looping, this will contain a subset of the settings array
     * @return mixed
     */
    public function getFromNestedKey($nested_key = array(), $partially_processed_array = null)
    {
        $partially_processed_array = (is_null($partially_processed_array)) ? $this : $partially_processed_array;
        if (count($nested_key) > 1) {
            $current_key = array_shift($nested_key);
            $partially_processed_array = $partially_processed_array[$current_key];

            return $this->getFromNestedKey($nested_key, $partially_processed_array);
        } else {
            return $partially_processed_array[array_shift($nested_key)];
        }
    }
}
