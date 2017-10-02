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
     * @return array
     */
    public static function subscribedEvents()
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
    }

    /**
     * @param PurchaseOrderCompletedEvent $event
     */
    public function onCompleted1(PurchaseOrderCompletedEvent $event)
    {
    }

    /**
     * @param PurchaseOrderCompletedEvent $event
     */
    public function onCompleted2(PurchaseOrderCompletedEvent $event)
    {
    }
}
