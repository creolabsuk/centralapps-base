<?php
namespace CentralApps\Base;

trait DebugTrait
{
    public function debug()
    {
        $this->removeContainer();

        return print_r($this, true);
    }

    public function removeContainer()
    {
        $this->container = '[== CONTAINER PLACEHOLDER ==]';
        foreach ($this as $key => $value) {
            if (is_object($value) && array_key_exists('CentralApps\Base\DebugTrait', class_uses($value))) {
                $value->removeContainer();
                $this->$key = $value;
            }
        }
    }
}
