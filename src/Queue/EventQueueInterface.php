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

interface EventQueueInterface
{
    /**
     * Push event to queue.
     *
     * @param EventInterface $event
     *
     * @return bool
     */
    public function push(EventInterface $event);

    /**
     * Pop event from queue. Return NULL if queue is empty.
     *
     * @return EventInterface|null
     */
    public function pop();
}
