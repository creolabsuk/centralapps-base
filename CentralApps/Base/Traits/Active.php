<?php
namespace CentralApps\Base\Traits;

trait Active
{
    protected $active = 1;

    public function getActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }

    public function isActive()
    {
        return (bool) $this->getActive();
    }
}
