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
     * @param EventQueue $queue
     */
    public function __construct(EventQueue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * Publishes the event into queue.
     *
     * @param Event $event
     */
    public function publish(Event $event)
    {
        $this->queue->publish($event);
    }

    /**
     * @param AggregateEvents $aggregator
     */
    public function pullAndPublish(AggregateEvents $aggregator)
    {
        foreach ($aggregator->pullEvents() as $event) {
            $this->queue->publish($event);
        }
    }
}
