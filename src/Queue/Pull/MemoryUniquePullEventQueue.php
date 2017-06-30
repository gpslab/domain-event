<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Queue\Pull;

use GpsLab\Domain\Event\Event;

class MemoryUniquePullEventQueue implements PullEventQueue
{
    /**
     * @var Event[]
     */
    private $events = [];

    /**
     * Publish event to queue.
     *
     * @param Event $event
     *
     * @return bool
     */
    public function publish(Event $event)
    {
        $index = array_search($event, $this->events);

        // remove exists event and publish it again
        if ($index !== false) {
            unset($this->events[$index]);
        }

        $this->events[] = $event;

        return true;
    }

    /**
     * Pop event from queue. Return NULL if queue is empty.
     *
     * @return Event|null
     */
    public function pull()
    {
        return array_shift($this->events);
    }
}
