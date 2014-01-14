<?php
namespace CentralApps\Base\Events;

interface EventListenerInterface
{

    /**
     * Get the events this event listener implements and should be bound to
     * @return array (keys are event names, values are either the method names, or an EventBinding object
     */
    public function getImplementedEvents();
    
    /**
     * Get the default priority for events, where a priority isn't already defined
     * @return int
     */
    public function getDefaultEventPriority();
}
