<?php

namespace CentralApps\Base\Events;

trait EventPropagationTrait
{
    protected $disableEventPropagation = false;

    public function disableEventPropagation()
    {
        $this->disableEventPropagation = true;
    }

    public function enableEventPropagation()
    {
        $this->disableEventPropagation = false;
    }

    public function propagateEvents()
    {
        return (!$this->disableEventPropagation);
    }

    public function toggleEventPropagation()
    {
        $this->disableEventPropagation = ! $this->disableEventPropagation;
    }
}
