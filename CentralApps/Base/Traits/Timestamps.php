<?php
namespace CentralApps\Base\Traits;

trait HasTimestampsTrait
{
    // TODO: consider type hinting for instances of DateTime?
    protected $created;
    protected $modified;

    public function setCreated($created)
    {
        $this->created = $created;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setModified($modified)
    {
        $this->modified = $modified;
    }

    public function getModified()
    {
        return $this->modified;
    }
}
