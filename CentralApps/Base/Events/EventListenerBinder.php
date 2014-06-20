<?php
namespace CentralApps\Base\Events;

abstract class EventListenerBinder
{
    protected function bindListener(\Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher, \CentralApps\Base\Events\EventListenerInterface $listener)
    {
        $listener_priority = $listener->getDefaultEventPriority();

        foreach ($listener->getImplementedEvents() as $event => $action) {
            if (is_string($action)) {
                $priority = $listener_priority;
            } elseif ($action instanceof \CentralApps\Base\Events\EventBinding) {
                $priority = (!is_null($action->priority)) ? $action->priority : $listener_priority;
                $action = $action->methodName;
            } else {
                throw new \LogicException("Unable to bind listeners");
            }

            $dispatcher->addListener($event, array($listener, $action), $priority);
        }

        return $dispatcher;
    }
}
