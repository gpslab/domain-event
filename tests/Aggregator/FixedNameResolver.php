<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Tests\Aggregator;

use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\NameResolver\EventNameResolverInterface;

class FixedNameResolver implements EventNameResolverInterface
{
    /**
     * @var string
     */
    private $name = '';

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getEventName(EventInterface $event)
    {
        return $this->name;
    }
}
