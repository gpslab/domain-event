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
use GpsLab\Domain\Event\Event;

class DemoAggregatorRaiseInSelf extends AbstractAggregateEventsRaiseInSelf
{
    /**
     * @var Event
     */
    private $raise_in_self_event;

    /**
     * @param Event $event
     */
    public function raiseEvent(Event $event)
    {
        $this->raise($event);
    }

    /**
     * @param Event $event
     */
    protected function onPurchaseOrderCreated(Event $event)
    {
        $this->raise_in_self_event = $event;
    }

    /**
     * @return Event
     */
    public function getRaiseInSelfEvent()
    {
        return $this->raise_in_self_event;
    }
}
