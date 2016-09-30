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
use GpsLab\Domain\Event\NameResolver\EventClassLastPartResolver;
use GpsLab\Domain\Event\NameResolver\EventNameResolverInterface;

trait AggregateEventsRaiseInSelfTrait
{
    /**
     * @var EventInterface[]
     */
    private $events = [];

    /**
     * @var EventNameResolverInterface
     */
    private $resolver;

    /**
     * @param EventNameResolverInterface $resolver
     */
    protected function changeEventNameResolver(EventNameResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @return EventNameResolverInterface
     */
    private function getEventNameResolver()
    {
        if (!($this->resolver instanceof EventNameResolverInterface)) {
            $this->resolver = new EventClassLastPartResolver(); // default name resolver
        }

        return $this->resolver;
    }

    /**
     * @param EventInterface $event
     */
    private function raiseInSelf(EventInterface $event)
    {
        $event_name = $this->getEventNameResolver()->getEventName($event);
        $method = sprintf('on%s', $event_name);

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
}
