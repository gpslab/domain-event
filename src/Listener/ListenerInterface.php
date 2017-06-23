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

/**
 * An event listener do some actions when a specific event is published.
 *
 * @deprecated It will be removed in 2.0. In 2.0 will use the type "callable" as listener.
 * @see http://php.net/manual/en/language.types.callable.php
 */
interface ListenerInterface
{
    /**
     * @param EventInterface $event
     */
    public function handle(EventInterface $event);
}
