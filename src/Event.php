<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event;

/**
 * Domain Events happen an Aggregate is modified.
 *
 * They are assumed be immutable objects that cannot change after instantiation.
 * Changing events can cause weird problems, so avoid this.
 */
interface Event
{
}
