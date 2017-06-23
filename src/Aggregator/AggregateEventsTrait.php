<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Aggregator;

use GpsLab\Domain\Event\Event;

trait AggregateEventsTrait
{
    /**
     * @var Event[]
     */
    private $events = [];

    /**
     * @param Event $event
     */
    protected function raise(Event $event)
    {
        $this->events[] = $event;
    }

    /**
     * @return Event[]
     */
    public function pullEvents()
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }
}
