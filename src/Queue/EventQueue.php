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

interface EventQueue
{
    /**
     * Push event to queue.
     *
     * @param Event $event
     *
     * @return bool
     */
    public function push(Event $event);

    /**
     * Pop event from queue. Return NULL if queue is empty.
     *
     * @return Event|null
     */
    public function pop();
}
