<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Tests\Aggregator;

use GpsLab\Domain\Event\Aggregator\AbstractAggregateEvents;
use GpsLab\Domain\Event\EventInterface;

class DemoAggregator extends AbstractAggregateEvents
{
    /**
     * @param EventInterface $event
     */
    public function raiseEvent(EventInterface $event)
    {
        $this->raise($event);
    }
}
