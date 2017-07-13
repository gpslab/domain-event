<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Queue\Subscribe;

use GpsLab\Domain\Event\Queue\EventQueue;

/**
 * Publish and Subscribe event queue.
 */
interface SubscribeEventQueue extends EventQueue
{
    /**
     * Subscribe on event queue.
     *
     * @param callable $handler
     */
    public function subscribe(callable $handler);

    /**
     * Unsubscribe on event queue.
     *
     * @param callable $handler
     */
    public function unsubscribe(callable $handler);
}
