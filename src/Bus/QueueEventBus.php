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
use GpsLab\Domain\Event\Queue\EventQueue;

class QueueEventBus implements EventBus
{
    /**
     * @var EventQueue
     */
    private $queue;

    /**
     * @var EventBus
     */
    private $publisher_bus;

    /**
     * @param EventQueue $queue
     * @param EventBus $publisher_bus
     */
    public function __construct(EventQueue $queue, EventBus $publisher_bus)
    {
        $this->queue = $queue;
        $this->publisher_bus = $publisher_bus;
    }

    /**
     * Publishes the event $event to every EventListener that wants to.
     *
     * @param Event $event
     */
    public function publish(Event $event)
    {
        $this->queue->push($event);
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

    /**
     * Publishes the events from event queue to the publisher bus.
     */
    public function publishFromQueue()
    {
        while (true) {
            $event = $this->queue->pop();

            if (!($event instanceof Event)) { // it's a end of queue
                break;
            }

            $this->publisher_bus->publish($event);
        }
    }
}
