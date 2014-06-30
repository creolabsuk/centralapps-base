<?php
namespace CentralApps\Base\Traits;

trait Dispatcher
{
    public function dispatch($event_name, $payload = null)
    {
        $standard_event = $this->container['standard_event']($payload);
        $this->container['dispatcher']->dispatch($event_name, $standard_event);
    }
}
