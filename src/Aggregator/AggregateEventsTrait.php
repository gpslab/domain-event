<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace GpsLab\Domain\Event\Aggregator;

use GpsLab\Domain\Event\EventInterface;

trait AggregateEventsTrait
{
    /**
     * @var EventInterface[]
     */
    private $events = [];

    /**
     * @return EventInterface[]
     */
    public function pullEvents()
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }

    /**
     * @param EventInterface $event
     */
    protected function raise(EventInterface $event)
    {
        $this->events[] = $event;
    }
}
