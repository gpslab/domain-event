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
use GpsLab\Domain\Event\Listener\Locator\SymfonyContainerEventListenerLocator;
use GpsLab\Domain\Event\Tests\Fixture\Listener\PurchaseOrderCompletedEventListener;
use GpsLab\Domain\Event\Tests\Fixture\Listener\PurchaseOrderCreatedEventListener;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SymfonyContainerEventListenerLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SymfonyContainerEventListenerLocator
     */
    private $locator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ContainerInterface
     */
    private $container;

    protected function setUp()
    {
        $this->container = $this->getMock(ContainerInterface::class);
        $this->locator = new SymfonyContainerEventListenerLocator();
    }

    private function setContainer()
    {
        $this->locator->setContainer($this->container);
    }

    public function testRegisterService()
    {
        $this->setContainer();

        /* @var $event Event */
        $event = $this->getMock(Event::class);

        $this->locator->registerService('foo', 'domain.listener.1');

        $listener2 = new PurchaseOrderCreatedEventListener();
        $this->locator->registerService(get_class($event), 'domain.listener.2');

        $this->container
            ->expects($this->once())
            ->method('get')
            ->with('domain.listener.2')
            ->will($this->returnValue($listener2))
        ;

        // test get event listeners for event
        $this->assertEquals([$listener2], $this->locator->listenersOfEvent($event));
    }

    public function testRegisterServiceNoListenersForEvent()
    {
        $this->setContainer();

        /* @var $event Event */
        $event = $this->getMock(Event::class);

        $this->locator->registerService('foo', 'domain.listener');

        $this->container
            ->expects($this->never())
            ->method('get')
        ;

        $this->assertEquals([], $this->locator->listenersOfEvent($event));
    }

    public function testRegisterServiceIsNotAListener()
    {
        $this->setContainer();

        /* @var $event Event */
        $event = $this->getMock(Event::class);

        $this->locator->registerService(get_class($event), 'domain.listener');

        $this->container
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue('bar'))
        ;

        $this->assertEquals([], $this->locator->listenersOfEvent($event));
    }

    public function testOverrideListener()
    {
        $this->setContainer();

        /* @var $event Event */
        $event = $this->getMock(Event::class);

        $listener1 = function (Event $event) {};
        $this->locator->registerService(get_class($event), 'domain.listener');

        $this->container
            ->expects($this->at(0))
            ->method('get')
            ->with('domain.listener')
            ->will($this->returnValue($listener1))
        ;

        $listeners = $this->locator->listenersOfEvent($event);
        $this->assertEquals([$listener1], $listeners);

        $listener2 = new PurchaseOrderCompletedEventListener();
        $this->locator->registerService(get_class($event), 'domain.listener', 'handle');

        $this->container
            ->expects($this->at(1))
            ->method('get')
            ->with('domain.listener')
            ->will($this->returnValue($listener2))
        ;

        $this->assertEquals([[$listener2, 'handle']], $this->locator->listenersOfEvent($event));
    }
}
