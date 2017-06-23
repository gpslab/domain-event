<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Bus;

use GpsLab\Domain\Event\Aggregator\AggregateEventsInterface;
use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\Queue\EventQueueInterface;

class QueueEventBus implements EventBus
{
    /**
     * @var EventQueueInterface
     */
    private $queue;

    /**
     * @var EventBus
     */
    private $publisher_bus;

    /**
     * @param EventQueueInterface $queue
     * @param EventBus $publisher_bus
     */
    public function __construct(EventQueueInterface $queue, EventBus $publisher_bus)
    {
        $this->queue = $queue;
        $this->publisher_bus = $publisher_bus;
    }

    /**
     * Publishes the event $event to every EventListener that wants to.
     *
     * @param EventInterface $event
     */
    public function publish(EventInterface $event)
    {
        $this->queue->push($event);
    }

    /**
     * @param AggregateEventsInterface $aggregator
     */
    public function pullAndPublish(AggregateEventsInterface $aggregator)
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

            if (!($event instanceof EventInterface)) { // it's a end of queue
                break;
            }

            $this->publisher_bus->publish($event);
        }
    }
}
