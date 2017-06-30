<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Queue\Subscribe;

use GpsLab\Domain\Event\Event;

class ExecutingSubscribeEventQueue implements SubscribeEventQueue
{
    /**
     * @var callable|null
     */
    private $handler;

    /**
     * Publish event to queue.
     *
     * @param Event $event
     *
     * @return bool
     */
    public function publish(Event $event)
    {
        // absence of a handler is not a error
        if (is_callable($this->handler)) {
            call_user_func($this->handler, $event);
        }

        return true;
    }

    /**
     * Subscribe on event queue.
     *
     * @param callable $handler
     */
    public function subscribe(callable $handler)
    {
        $this->handler = $handler;
    }
}
