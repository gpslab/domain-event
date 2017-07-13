<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Tests\Fixture\Listener;

use GpsLab\Domain\Event\Tests\Fixture\PurchaseOrderCreatedEvent;

class PurchaseOrderCreatedEventListener
{
    /**
     * @var PurchaseOrderCreatedEvent|null
     */
    private $event;

    /**
     * @param PurchaseOrderCreatedEvent $event
     */
    public function __invoke(PurchaseOrderCreatedEvent $event)
    {
        $this->event = $event;
    }

    /**
     * @return PurchaseOrderCreatedEvent|null
     */
    public function handledEvent()
    {
        return $this->event;
    }
}
