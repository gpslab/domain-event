<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Queue;

use GpsLab\Domain\Event\EventInterface;

class MemoryEventQueue implements EventQueueInterface
{
    /**
     * @var EventInterface[]
     */
    private $events = [];

    /**
     * Push event to queue.
     *
     * @param EventInterface $event
     *
     * @return bool
     */
    public function push(EventInterface $event)
    {
        $this->events[] = $event;

        return true;
    }

    /**
     * Pop event from queue. Return NULL if queue is empty.
     *
     * @return EventInterface|null
     */
    public function pop()
    {
        return array_shift($this->events);
    }
}
