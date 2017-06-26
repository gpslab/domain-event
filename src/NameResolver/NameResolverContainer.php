<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\NameResolver;

/**
 * It's a global container for get event name resolver in entities.
 *
 * It's need for not duplicate code
 *
 * @see AggregateEventsRaiseInSelfTrait::getMethodNameFromEvent()
 */
class NameResolverContainer
{
    /**
     * @var EventNameResolverInterface
     */
    private static $resolver;

    /**
     * @param EventNameResolverInterface $resolver
     */
    public static function changeResolver(EventNameResolverInterface $resolver)
    {
        self::$resolver = $resolver;
    }

    /**
     * @return EventNameResolverInterface
     */
    public static function getResolver()
    {
        if (!(self::$resolver instanceof EventNameResolverInterface)) {
            self::$resolver = new EventClassLastPartResolver(); // default name resolver
        }

        return self::$resolver;
    }
}
