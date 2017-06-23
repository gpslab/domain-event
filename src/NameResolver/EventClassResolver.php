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

class EventClassResolver implements EventNameResolverInterface
{
    /**
     * @param Event $event
     *
     * @return string
     */
    public function getEventName(Event $event)
    {
        return get_class($event);
    }
}
