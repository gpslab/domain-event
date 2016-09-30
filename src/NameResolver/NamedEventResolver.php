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
use GpsLab\Domain\Event\Exception\InvalidEventException;
use GpsLab\Domain\Event\NamedEventInterface;

class NamedEventResolver implements EventNameResolverInterface
{
    /**
     * @param EventInterface $event
     *
     * @return string
     */
    public function getEventName(EventInterface $event)
    {
        if (!($event instanceof NamedEventInterface)) {
            throw new InvalidEventException(sprintf(
                'Event "%s" must be instance of "%s".',
                get_class($event),
                NamedEventInterface::class
            ));
        }

        return $event->getName();
    }
}
