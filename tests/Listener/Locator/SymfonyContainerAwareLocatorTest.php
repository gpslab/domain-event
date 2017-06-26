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
use GpsLab\Domain\Event\Listener\Locator\SymfonyContainerAwareLocator;
use GpsLab\Domain\Event\NameResolver\EventNameResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SymfonyContainerAwareLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SymfonyContainerAwareLocator
     */
    private $locator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|EventNameResolverInterface
     */
    private $resolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ContainerInterface
     */
    private $container;

    protected function setUp()
    {
        $this->resolver = $this->getMock(EventNameResolverInterface::class);
        $this->container = $this->getMock(ContainerInterface::class);
        $this->locator = new SymfonyContainerAwareLocator($this->resolver);
        $this->locator->setContainer($this->container);
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
        $this->locator->registerService('bar', 'domain.listener.3');

        /* @var $listener4 ListenerInterface */
        $listener4 = $this->getMock(ListenerInterface::class);
        $this->locator->registerService('bar', 'domain.listener.4');

        $this->container
            ->expects($this->at(0))
            ->method('get')
            ->with('domain.listener.3')
            ->will($this->returnValue($listener3))
        ;
        $this->container
            ->expects($this->at(1))
            ->method('get')
            ->with('domain.listener.4')
            ->will($this->returnValue($listener4))
        ;

        $this->resolver
            ->expects($this->atLeastOnce())
            ->method('getEventName')
            ->with($event)
            ->will($this->returnValue('bar'))
        ;

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

        $this->locator->registerService('foo', 'domain.listener.2');

        $this->container
            ->expects($this->never())
            ->method('get');

        $this->resolver
            ->expects($this->atLeastOnce())
            ->method('getEventName')
            ->with($event)
            ->will($this->returnValue('bar'));

        $listeners = $this->locator->listenersOfEvent($event);
        $this->assertInstanceOf(ListenerCollection::class, $listeners);
        $this->assertEquals(new ListenerCollection(), $listeners);
    }

    public function testOverrideListener()
    {
        /* @var $event Event */
        $event = $this->getMock(Event::class);

        /* @var $listener1 ListenerInterface */
        $listener1 = $this->getMock(ListenerInterface::class);
        $this->locator->registerService('foo', 'domain.listener');

        $this->container
            ->expects($this->at(0))
            ->method('get')
            ->with('domain.listener')
            ->will($this->returnValue($listener1))
        ;

        $this->resolver
            ->expects($this->atLeastOnce())
            ->method('getEventName')
            ->with($event)
            ->will($this->returnValue('foo'));

        $listeners = $this->locator->listenersOfEvent($event);
        $this->assertInstanceOf(ListenerCollection::class, $listeners);
        $this->assertEquals(new ListenerCollection([$listener1]), $listeners);

        /* @var $listener2 ListenerInterface */
        $listener2 = $this->getMock(ListenerInterface::class);
        $this->locator->registerService('foo', 'domain.listener');

        $this->container
            ->expects($this->at(1))
            ->method('get')
            ->with('domain.listener')
            ->will($this->returnValue($listener2))
        ;

        $listeners = $this->locator->listenersOfEvent($event);
        $this->assertEquals(new ListenerCollection([$listener2]), $listeners);
    }
}
