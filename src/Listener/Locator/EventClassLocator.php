<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace GpsLab\Domain\Event\Listener\Locator;

use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\Listener\ListenerCollection;
use GpsLab\Domain\Event\Listener\ListenerInterface;

class EventClassLocator implements LocatorInterface
{
    /**
     * @var ListenerCollection[]
     */
    private $listeners = [];

    /**
     * @param EventInterface $event
     *
     * @return ListenerInterface[]
     */
    public function getListenersForEvent(EventInterface $event)
    {
        $event_class = get_class($event);

        if (isset($this->listeners[$event_class])) {
            return $this->listeners[$event_class];
        } else {
            return new ListenerCollection();
        }
    }

    /**
     * @param string $event_class
     * @param ListenerInterface $listener
     */
    public function register($event_class, ListenerInterface $listener)
    {
        if (!isset($this->listeners[$event_class])) {
            $this->listeners[$event_class] = new ListenerCollection();
        }

        $this->listeners[$event_class]->add($listener);
    }

    /**
     * @return ListenerCollection
     */
    public function getRegisteredEventListeners()
    {
        $listeners = [];
        foreach ($this->listeners as $listener_collection) {
            $listeners = array_merge($listeners, $listener_collection);
        }

        return new ListenerCollection($listeners);
    }
}
