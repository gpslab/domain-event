<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Bus;

use GpsLab\Domain\Event\Aggregator\AggregateEvents;
use GpsLab\Domain\Event\Event;
use GpsLab\Domain\Event\Listener\Locator\EventListenerLocator;

class ListenerLocatedEventBus implements EventBus
{
    /**
     * @var EventListenerLocator
     */
    private $locator;

    /**
     * @param EventListenerLocator $locator
     */
    public function __construct(EventListenerLocator $locator)
    {
        $this->locator = $locator;
    }

    /**
     * Publishes the event to every listener that wants to.
     *
     * @param Event $event
     */
    public function publish(Event $event)
    {
        foreach ($this->locator->listenersOfEvent($event) as $listener) {
            call_user_func($listener, $event);
        }
    }

    /**
     * @param AggregateEvents $aggregator
     */
    public function pullAndPublish(AggregateEvents $aggregator)
    {
        foreach ($aggregator->pullEvents() as $event) {
            $this->publish($event);
        }
    }
}
