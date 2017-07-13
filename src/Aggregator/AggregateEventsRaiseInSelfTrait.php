<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Aggregator;

use GpsLab\Domain\Event\Event;

trait AggregateEventsRaiseInSelfTrait
{
    /**
     * @var Event[]
     */
    private $events = [];

    /**
     * @param Event $event
     */
    private function raiseInSelf(Event $event)
    {
        $method = $this->eventHandlerName($event);

        // if method is not exists is not a critical error
        if (method_exists($this, $method)) {
            $this->{$method}($event);
        }
    }

    /**
     * @param Event $event
     */
    protected function raise(Event $event)
    {
        $this->events[] = $event;
        $this->raiseInSelf($event);
    }

    /**
     * @return Event[]
     */
    public function pullEvents()
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }

    /**
     * Get handler method name from event.
     *
     * Override this method if you want to change algorithm to generate the handler method name.
     *
     * @param Event $event
     *
     * @return string
     */
    protected function eventHandlerName(Event $event)
    {
        $class = get_class($event);

        if ('Event' === substr($class, -5)) {
            $class = substr($class, 0, -5);
        }

        $class = str_replace('_', '\\', $class); // convert names for classes not in namespace
        $parts = explode('\\', $class);

        return 'on'.end($parts);
    }
}
