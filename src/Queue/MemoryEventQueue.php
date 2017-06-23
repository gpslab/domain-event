<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Queue;

use GpsLab\Domain\Event\Event;

class MemoryEventQueue implements EventQueue
{
    /**
     * @var Event[]
     */
    private $events = [];

    /**
     * Push event to queue.
     *
     * @param Event $event
     *
     * @return bool
     */
    public function push(Event $event)
    {
        $this->events[] = $event;

        return true;
    }

    /**
     * Pop event from queue. Return NULL if queue is empty.
     *
     * @return Event|null
     */
    public function pop()
    {
        return array_shift($this->events);
    }
}
