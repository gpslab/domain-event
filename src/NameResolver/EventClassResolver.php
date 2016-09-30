<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace GpsLab\Domain\Event\NameResolver;

use GpsLab\Domain\Event\EventInterface;

class EventClassResolver implements EventNameResolverInterface
{
    /**
     * @param EventInterface $event
     *
     * @return string
     */
    public function getEventName(EventInterface $event)
    {
        return get_class($event);
    }
}