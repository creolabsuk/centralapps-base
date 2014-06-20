<?php
namespace CentralApps\Base\Events;

class StandardEvent extends \Symfony\Component\EventDispatcher\Event
{
    protected $payload = null;

    public function __construct($payload = null)
    {
        if (!is_null($payload)) {
            $this->payload = $payload;
        }
    }

    public function setPayload($payload)
    {
        $this->payload = $payload;
    }

    public function getPayload()
    {
        return $this->payload;
    }
}
