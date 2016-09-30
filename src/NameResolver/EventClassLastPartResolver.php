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

class EventClassLastPartResolver implements EventNameResolverInterface
{
    /**
     * @param EventInterface $event
     *
     * @return string
     */
    public function getEventName(EventInterface $event)
    {
        $class = get_class($event);

        if ('Event' === substr($class, -5)) {
            $class = substr($class, 0, -5);
        }

        $class = str_replace('_', '\\', $class); // convert names for classes not in namespace
        $parts = explode('\\', $class);

        return end($parts);
    }
}
