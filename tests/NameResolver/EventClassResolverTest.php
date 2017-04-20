<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Tests\NameResolver;

use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\NameResolver\EventClassResolver;

class EventClassResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventClassResolver
     */
    private $resolver;

    protected function setUp()
    {
        $this->resolver = new EventClassResolver();
    }

    public function testGetEventName()
    {
        /* @var $event EventInterface */
        $event = $this->getMock(EventInterface::class);

        $this->assertEquals(get_class($event), $this->resolver->getEventName($event));
    }
}
