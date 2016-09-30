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
use GpsLab\Domain\Event\NamedEventInterface;
use GpsLab\Domain\Event\NameResolver\NamedEventResolver;

class NamedEventResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NamedEventResolver
     */
    private $resolver;

    protected function setUp()
    {
        $this->resolver = new NamedEventResolver();
    }

    /**
     * @expectedException \GpsLab\Domain\Event\Exception\InvalidEventException
     */
    public function testGetEventNameForNotNamedEvent()
    {
        /* @var $event EventInterface */
        $event = $this->getMock(EventInterface::class);

        $this->resolver->getEventName($event);
    }

    public function testGetEventName()
    {
        $name = 'foo';

        /* @var $event \PHPUnit_Framework_MockObject_MockObject|NamedEventInterface */
        $event = $this->getMock(NamedEventInterface::class);
        $event
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name));

        $this->assertEquals($name, $this->resolver->getEventName($event));
    }
}
