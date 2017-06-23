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
use GpsLab\Domain\Event\NameResolver\NameResolverContainer;

trait AggregateEventsRaiseInSelfTrait
{
    /**
     * @var EventInterface[]
     */
    private $events = [];

    /**
     * @param EventInterface $event
     */
    private function raiseInSelf(EventInterface $event)
    {
        $method = $this->getMethodNameFromEvent($event);

        // if method is not exists is not a critical error
        if (method_exists($this, $method)) {
            $this->{$method}($event);
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
        return 'on'.NameResolverContainer::getResolver()->getEventName($event);
    }
}
