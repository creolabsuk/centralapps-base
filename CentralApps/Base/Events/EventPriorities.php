<?php
namespace CentralApps\Base\Events;

class EventPriorities
{
    const DEFAULT_PRIORITY = 10;
    const API_PRIORITY = 20;
    const QUEUEABLE_PRIORITY = 30;
    
    const CRITIAL_PRIORITY = 100;
    const HIGH_PRIORITY = 50;
    const LOW_PRIORITY = 0;
    const NORMAL_PRIORITY = 10;
}
