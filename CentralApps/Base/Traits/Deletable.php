<?php
namespace CentralApps\Base\Traits;

trait Deletable
{
    protected $deleted = 0;

    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function isDeleted()
    {
        return (bool) $this->getDeleted();
    }
}
