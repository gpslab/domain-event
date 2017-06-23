<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\NameResolver;

use GpsLab\Domain\Event\Event;
use GpsLab\Domain\Event\Exception\InvalidEventException;
use GpsLab\Domain\Event\NamedEvent;

class NamedEventResolver implements EventNameResolverInterface
{
    /**
     * @param Event $event
     *
     * @return string
     */
    public function getEventName(Event $event)
    {
        if (!($event instanceof NamedEvent)) {
            throw new InvalidEventException(sprintf(
                'Event "%s" must be instance of "%s".',
                get_class($event),
                NamedEvent::class
            ));
        }

        return $event->getName();
    }
}
