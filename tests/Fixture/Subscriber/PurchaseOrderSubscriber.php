<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Tests\Fixture\Subscriber;

use GpsLab\Domain\Event\Listener\Subscriber;
use GpsLab\Domain\Event\Tests\Fixture\PurchaseOrderCompletedEvent;
use GpsLab\Domain\Event\Tests\Fixture\PurchaseOrderCreatedEvent;

class PurchaseOrderSubscriber implements Subscriber
{
    /**
     * @var PurchaseOrderCreatedEvent
     */
    private $created_event;

    /**
     * @var PurchaseOrderCompletedEvent
     */
    private $completed_event1;

    /**
     * @var PurchaseOrderCompletedEvent
     */
    private $completed_event2;

    /**
     * @return array
     */
    public function subscribedEvents()
    {
        return [
            PurchaseOrderCreatedEvent::class => ['onCreated'],
            PurchaseOrderCompletedEvent::class => ['onCompleted1', 'onCompleted2'],
        ];
    }

    /**
     * @param PurchaseOrderCreatedEvent $event
     */
    public function onCreated(PurchaseOrderCreatedEvent $event)
    {
        $this->created_event = $event;
    }

    /**
     * @param PurchaseOrderCompletedEvent $event
     */
    public function onCompleted1(PurchaseOrderCompletedEvent $event)
    {
        $this->completed_event1 = $event;
    }

    /**
     * @param PurchaseOrderCompletedEvent $event
     */
    public function onCompleted2(PurchaseOrderCompletedEvent $event)
    {
        $this->completed_event2 = $event;
    }

    /**
     * @return PurchaseOrderCreatedEvent
     */
    public function createdEvent()
    {
        return $this->created_event;
    }

    /**
     * @return PurchaseOrderCompletedEvent
     */
    public function completedEvent1()
    {
        return $this->completed_event1;
    }

    /**
     * @return PurchaseOrderCompletedEvent
     */
    public function completedEvent2()
    {
        return $this->completed_event2;
    }
}
