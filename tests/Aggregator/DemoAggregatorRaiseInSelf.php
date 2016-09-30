<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace GpsLab\Domain\Event\Tests\Aggregator;

use GpsLab\Domain\Event\Aggregator\AbstractAggregateEventsRaiseInSelf;
use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\NameResolver\EventNameResolverInterface;

class DemoAggregatorRaiseInSelf extends AbstractAggregateEventsRaiseInSelf
{
    /**
     * @var EventInterface
     */
    private $raise_in_self_event;

    /**
     * @param EventInterface $event
     */
    public function raiseEvent(EventInterface $event)
    {
        $this->raise($event);
    }

    /**
     * @param EventNameResolverInterface $resolver
     */
    public function setEventNameResolver(EventNameResolverInterface $resolver)
    {
        $this->changeEventNameResolver($resolver);
    }

    /**
     * @param EventInterface $event
     */
    public function onPurchaseOrderCreated(EventInterface $event)
    {
        $this->raise_in_self_event = $event;
    }

    /**
     * @return EventInterface
     */
    public function getRaiseInSelfEvent()
    {
        return $this->raise_in_self_event;
    }
}
