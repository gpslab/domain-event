<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Bus;

use GpsLab\Domain\Event\Aggregator\AggregateEvents;
use GpsLab\Domain\Event\Event;

interface EventBus
{
    /**
     * @param Event $event
     */
    public function publish(Event $event);

    /**
     * @param AggregateEvents $aggregator
     */
    public function pullAndPublish(AggregateEvents $aggregator);
}
