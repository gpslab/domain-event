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
 */
interface ListenerInterface
{
    /**
     * @param EventInterface $event
     */
    public function handle(EventInterface $event);
}
