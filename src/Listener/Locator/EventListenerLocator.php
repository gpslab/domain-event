<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Listener\Locator;

use GpsLab\Domain\Event\Event;

/**
 * The purpose of this interface is to connect EventListeners to their Event.
 * You can have multiple Locator if you want to have multiple EventBus.
 */
interface EventListenerLocator
{
    /**
     * Get the list of every event listeners that want to be warn when the event specified in argument is published.
     *
     * @param Event $event
     *
     * @return callable[]
     */
    public function listenersOfEvent(Event $event);
}
