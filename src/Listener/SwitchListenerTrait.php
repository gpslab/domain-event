<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Listener;

use GpsLab\Domain\Event\Event;
use GpsLab\Domain\Event\NameResolver\NameResolverContainer;

trait SwitchListenerTrait
{
    /**
     * @param Event $event
     */
    public function handle(Event $event)
    {
        call_user_func([$this, $this->getMethodNameFromEvent($event)], $event);
    }

    /**
     * Get handler method name from event.
     *
     * Override this method if you want to change algorithm to generate the handler method name.
     *
     * @param Event $event
     *
     * @return string
     */
    protected function getMethodNameFromEvent(Event $event)
    {
        return 'handle'.NameResolverContainer::getResolver()->getEventName($event);
    }
}
