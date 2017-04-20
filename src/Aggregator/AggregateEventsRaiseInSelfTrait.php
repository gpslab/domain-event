<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Aggregator;

use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\NameResolver\EventNameResolverInterface;
use GpsLab\Domain\Event\NameResolver\NameResolverContainer;

trait AggregateEventsRaiseInSelfTrait
{
    /**
     * @var EventInterface[]
     */
    private $events = [];

    /**
     * @deprecated It will be removed in 2.0
     *
     * @var EventNameResolverInterface
     */
    private $resolver;

    /**
     * @deprecated It will be removed in 2.0. If you want change the event name resolver, you must override getMethodNameFromEvent() method.
     * @see AggregateEventsRaiseInSelfTrait::getMethodNameFromEvent()
     *
     * @param EventNameResolverInterface $resolver
     */
    protected function changeEventNameResolver(EventNameResolverInterface $resolver)
    {
        trigger_error('It will be removed in 2.0. If you want change the event name resolver, you must override getMethodNameFromEvent() method.', E_USER_DEPRECATED);

        $this->resolver = $resolver;
    }

    /**
     * @param EventInterface $event
     */
    private function raiseInSelf(EventInterface $event)
    {
        $method = $this->getMethodNameFromEvent($event);

        // if method is not exists is not a critical error
        if (method_exists($this, $method)) {
            call_user_func([$this, $method], $event);
        }
    }

    /**
     * @param EventInterface $event
     */
    protected function raise(EventInterface $event)
    {
        $this->events[] = $event;
        $this->raiseInSelf($event);
    }

    /**
     * @return EventInterface[]
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
     * @param EventInterface $event
     *
     * @return string
     */
    protected function getMethodNameFromEvent(EventInterface $event)
    {
        // BC: use custom event name resolver if exists
        if ($this->resolver instanceof EventNameResolverInterface) {
            return 'on'.$this->resolver->getEventName($event);
        }

        return 'on'.NameResolverContainer::getResolver()->getEventName($event);
    }
}
