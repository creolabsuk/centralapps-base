<?php

namespace CentralApps\Base\Views;

class TemplateVariables implements \ArrayAccess
{
    protected $data = array();

    public function offsetExists($offset)
    {
        return (isset($this->data[$offset]));
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function getVariables()
    {
        return $this->data;
    }
}
