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
     * @var callable[]
     */
    private $handlers = [];

    /**
     * Publish event to queue.
     *
     * @param Event $event
     *
     * @return bool
     */
    public function publish(Event $event)
    {
        // absence of a handlers is not a error
        foreach ($this->handlers as $handler) {
            call_user_func($handler, $event);
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
        $this->handlers[] = $handler;
    }

    /**
     * Unsubscribe on event queue.
     *
     * @param callable $handler
     *
     * @return bool
     */
    public function unsubscribe(callable $handler)
    {
        $index = array_search($handler, $this->handlers);

        if ($index === false) {
            return false;
        }

        unset($this->handlers[$index]);

        return true;
    }
}
