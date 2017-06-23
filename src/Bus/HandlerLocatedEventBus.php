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
use GpsLab\Domain\Event\Listener\Locator\Locator;

class HandlerLocatedEventBus implements EventBus
{
    /**
     * @var Locator
     */
    private $locator;

    /**
     * @param Locator $locator
     */
    public function __construct(Locator $locator)
    {
        $this->locator = $locator;
    }

    /**
     * Publishes the event $event to every EventListener that wants to.
     *
     * @param Event $event
     */
    public function publish(Event $event)
    {
        foreach ($this->locator->getListenersForEvent($event) as $listener) {
            $listener->handle($event);
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
