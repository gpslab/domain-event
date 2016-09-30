<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace GpsLab\Domain\Event\Aggregator;

abstract class AbstractAggregateEventsRaiseInSelf implements AggregateEventsInterface
{
    use AggregateEventsRaiseInSelfTrait;
}
