<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Tests\Listener\Locator;

use GpsLab\Domain\Event\Event;
use GpsLab\Domain\Event\Listener\ListenerCollection;
use GpsLab\Domain\Event\Listener\ListenerInterface;
use GpsLab\Domain\Event\Listener\Locator\NamedEventLocator;
use GpsLab\Domain\Event\NameResolver\EventNameResolverInterface;

class NamedEventLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NamedEventLocator
     */
    private $locator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|EventNameResolverInterface
     */
    private $resolver;

    protected function setUp()
    {
        $this->resolver = $this->getMock(EventNameResolverInterface::class);
        $this->locator = new NamedEventLocator($this->resolver);
    }

    public function testRegister()
    {
        /* @var $event Event */
        $event = $this->getMock(Event::class);

        /* @var $listener1 ListenerInterface */
        $listener1 = $this->getMock(ListenerInterface::class);
        $this->locator->register('foo', $listener1);

        /* @var $listener2 ListenerInterface */
        $listener2 = $this->getMock(ListenerInterface::class);
        $this->locator->register('foo', $listener2);

        /* @var $listener3 ListenerInterface */
        $listener3 = $this->getMock(ListenerInterface::class);
        $this->locator->register('bar', $listener3);

        /* @var $listener4 ListenerInterface */
        $listener4 = $this->getMock(ListenerInterface::class);
        $this->locator->register('bar', $listener4);

        $this->resolver
            ->expects($this->once())
            ->method('getEventName')
            ->with($event)
            ->will($this->returnValue('bar'));

        // test get event listeners for event
        $listeners = $this->locator->listenersOfEvent($event);
        $this->assertInstanceOf(ListenerCollection::class, $listeners);
        $this->assertEquals(new ListenerCollection([$listener3, $listener4]), $listeners);
    }

    public function testNoListenersForEvent()
    {
        /* @var $event Event */
        $event = $this->getMock(Event::class);

        /* @var $listener1 ListenerInterface */
        $listener1 = $this->getMock(ListenerInterface::class);
        $this->locator->register('foo', $listener1);

        /* @var $listener2 ListenerInterface */
        $listener2 = $this->getMock(ListenerInterface::class);
        $this->locator->register('foo', $listener2);

        $this->resolver
            ->expects($this->once())
            ->method('getEventName')
            ->with($event)
            ->will($this->returnValue('bar'));

        $listeners = $this->locator->listenersOfEvent($event);
        $this->assertInstanceOf(ListenerCollection::class, $listeners);
        $this->assertEquals(new ListenerCollection(), $listeners);
    }
}
