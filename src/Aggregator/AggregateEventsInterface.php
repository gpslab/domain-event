<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace GpsLab\Domain\Event\Aggregator;

use GpsLab\Domain\Event\EventInterface;

interface AggregateEventsInterface
{
    /**
     * @return EventInterface[]
     */
    public function pullEvents();
}
