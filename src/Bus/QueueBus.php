<?php
/**
 * Pkvs package
 *
 * @package Pkvs
 * @author  Peter Gribanov <pgribanov@1tv.com>
 */

namespace GpsLab\Domain\Event\Bus;

use GpsLab\Domain\Event\Aggregator\AggregateEventsInterface;
use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\Listener\ListenerCollection;
use GpsLab\Domain\Event\Listener\ListenerInterface;
use GpsLab\Domain\Event\Queue\EventQueueInterface;

class QueueBus implements BusInterface
{
    /**
     * @var EventQueueInterface
     */
    private $queue;

    /**
     * @var BusInterface
     */
    private $publisher_bus;

    /**
     * @param EventQueueInterface $queue
     * @param BusInterface $publisher_bus
     */
    public function __construct(EventQueueInterface $queue, BusInterface $publisher_bus)
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

    /**
     * @return ListenerInterface[]|ListenerCollection
     */
    public function getRegisteredEventListeners()
    {
        return $this->publisher_bus->getRegisteredEventListeners();
    }
}
