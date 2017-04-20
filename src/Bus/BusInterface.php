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
use GpsLab\Domain\Event\Listener\ListenerCollection;
use GpsLab\Domain\Event\Listener\ListenerInterface;

interface BusInterface
{
    /**
     * @param EventInterface $event
     */
    public function publish(EventInterface $event);

    /**
     * @param AggregateEventsInterface $aggregator
     */
    public function pullAndPublish(AggregateEventsInterface $aggregator);

    /**
     * Get the list of every EventListener defined in the EventBus.
     * This might be useful for debug.
     *
     * @return ListenerInterface[]|ListenerCollection
     */
    public function getRegisteredEventListeners();
}
