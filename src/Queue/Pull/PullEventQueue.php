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
use GpsLab\Domain\Event\Queue\EventQueue;

interface PullEventQueue extends EventQueue
{
    /**
     * Pull event from queue. Return NULL if queue is empty.
     *
     * @return Event|null
     */
    public function pull();
}
