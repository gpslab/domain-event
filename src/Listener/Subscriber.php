<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Listener;

interface Subscriber
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * For instance:
     *
     *  ['eventName' => ['methodName1', 'methodName2']
     *
     * @return array
     */
    public function subscribedEvents();
}
