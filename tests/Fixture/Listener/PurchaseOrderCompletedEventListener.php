<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Tests\Fixture\Listener;

use GpsLab\Domain\Event\Tests\Fixture\PurchaseOrderCompletedEvent;

class PurchaseOrderCompletedEventListener
{
    /**
     * @var PurchaseOrderCompletedEvent|null
     */
    private $event;

    /**
     * @param PurchaseOrderCompletedEvent $event
     */
    public function handle(PurchaseOrderCompletedEvent $event)
    {
        $this->event = $event;
    }

    /**
     * @return PurchaseOrderCompletedEvent|null
     */
    public function handledEvent()
    {
        return $this->event;
    }
}
