<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Listener\Locator;

use GpsLab\Domain\Event\Event;

class DirectBindingEventListenerLocator implements EventListenerLocator
{
    /**
     * @var callable[][]
     */
    private $listeners = [];

    /**
     * @param Event $event
     *
     * @return callable[]
     */
    public function listenersOfEvent(Event $event)
    {
        $event_name = get_class($event);

        return isset($this->listeners[$event_name]) ? $this->listeners[$event_name] : [];
    }

    /**
     * @param string   $event_name
     * @param callable $listener
     */
    public function register($event_name, callable $listener)
    {
        if (!isset($this->listeners[$event_name])) {
            $this->listeners[$event_name] = [];
        }

        $this->listeners[$event_name][] = $listener;
    }
}
