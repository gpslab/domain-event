<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Listener;

/**
 * @deprecated It will be removed in 2.0. In 2.0 will use the type "callable" as listener.
 * @see http://php.net/manual/en/language.types.callable.php
 */
abstract class AbstractSwitchListener implements ListenerInterface
{
    use SwitchListenerTrait;
}
