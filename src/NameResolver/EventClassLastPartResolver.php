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
        $name = get_class($event);
        $name = str_replace('_', '\\', $name);
        $name = explode('\\', $name);

        return end($name);
    }
}
