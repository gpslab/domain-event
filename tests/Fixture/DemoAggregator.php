<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Tests\Fixture;

use GpsLab\Domain\Event\Aggregator\AbstractAggregateEvents;
use GpsLab\Domain\Event\Event;

class DemoAggregator extends AbstractAggregateEvents
{
    /**
     * @param Event $event
     */
    public function raiseEvent(Event $event)
    {
        $this->raise($event);
    }
}
