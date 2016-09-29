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

interface VoterListenerInterface extends ListenerInterface
{
    /**
     * @param EventInterface $event
     *
     * @return bool
     */
    public function isSupportedEvent(EventInterface $event);
}
