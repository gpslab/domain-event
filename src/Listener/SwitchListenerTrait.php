<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Listener;

use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\NameResolver\NameResolverContainer;

/**
 * @deprecated It will be removed in 2.0. In 2.0 will use the type "callable" as listener.
 * @see http://php.net/manual/en/language.types.callable.php
 */
trait SwitchListenerTrait
{
    /**
     * @param EventInterface $event
     */
    public function handle(EventInterface $event)
    {
        call_user_func([$this, $this->getMethodNameFromEvent($event)], $event);
    }

    /**
     * Get handler method name from event.
     *
     * Override this method if you want to change algorithm to generate the handler method name.
     *
     * @param EventInterface $event
     *
     * @return string
     */
    protected function getMethodNameFromEvent(EventInterface $event)
    {
        return 'handle'.NameResolverContainer::getResolver()->getEventName($event);
    }
}
