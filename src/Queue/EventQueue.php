<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Queue;

use GpsLab\Domain\Event\Event;

interface EventQueue
{
    /**
     * Publish event to queue.
     *
     * @param Event $event
     *
     * @return bool
     */
    public function publish(Event $event);
}
