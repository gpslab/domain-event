<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Listener\Locator;

use GpsLab\Domain\Event\Listener\Subscriber;

class DirectBindingEventSubscriberLocator extends DirectBindingEventListenerLocator
{
    /**
     * @param Subscriber $subscriber
     */
    public function registerSubscriber(Subscriber $subscriber)
    {
        foreach ($subscriber->subscribedEvents() as $event_name => $methods) {
            foreach ($methods as $method) {
                $this->register($event_name, [$subscriber, $method]);
            }
        }
    }
}
