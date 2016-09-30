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
use GpsLab\Domain\Event\NameResolver\EventNameResolverInterface;

class NamedEventLocator implements LocatorInterface
{
    /**
     * @var ListenerCollection[]
     */
    private $listeners = [];

    /**
     * @var EventNameResolverInterface
     */
    private $resolver;

    /**
     * @param EventNameResolverInterface $resolver
     */
    public function __construct(EventNameResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @param EventInterface $event
     *
     * @return ListenerInterface[]
     */
    public function getListenersForEvent(EventInterface $event)
    {
        $event_name = $this->resolver->getEventName($event);

        if (isset($this->listeners[$event_name])) {
            return $this->listeners[$event_name];
        } else {
            return new ListenerCollection();
        }
    }

    /**
     * @param string $event_name
     * @param ListenerInterface $listener
     */
    public function register($event_name, ListenerInterface $listener)
    {
        if (!isset($this->listeners[$event_name])) {
            $this->listeners[$event_name] = new ListenerCollection();
        }

        $this->listeners[$event_name]->add($listener);
    }

    /**
     * @return ListenerCollection
     */
    public function getRegisteredEventListeners()
    {
        $listeners = [];
        foreach ($this->listeners as $listener_collection) {
            $listeners = array_merge($listeners, (array) $listener_collection->getIterator());
        }

        return new ListenerCollection($listeners);
    }
}
