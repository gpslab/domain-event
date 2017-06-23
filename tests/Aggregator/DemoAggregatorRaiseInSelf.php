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
     * @param EventInterface $event
     */
    protected function onPurchaseOrderCreated(EventInterface $event)
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
