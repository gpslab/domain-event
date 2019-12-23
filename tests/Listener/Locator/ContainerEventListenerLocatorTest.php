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
use GpsLab\Domain\Event\Listener\Locator\ContainerEventListenerLocator;
use GpsLab\Domain\Event\Tests\Fixture\Listener\PurchaseOrderCreatedEventListener;
use GpsLab\Domain\Event\Tests\Fixture\PurchaseOrderCompletedEvent;
use GpsLab\Domain\Event\Tests\Fixture\PurchaseOrderCreatedEvent;
use GpsLab\Domain\Event\Tests\Fixture\Subscriber\PurchaseOrderSubscriber;
use Psr\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class ContainerEventListenerLocatorTest extends TestCase
{
    /**
     * @var ContainerEventListenerLocator
     */
    private $locator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ContainerInterface
     */
    private $container;

    protected function setUp()
    {
        $this->container = $this->getMock(ContainerInterface::class);
        $this->locator = new ContainerEventListenerLocator($this->container);
    }

    public function testRegisterService()
    {
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
        // test double call
        $this->assertEquals([$listener2], $this->locator->listenersOfEvent($event));
    }

    public function testRegisterServiceNoListenersForEvent()
    {
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

    public function testRegisterSubscriber()
    {
        $subscriber = new PurchaseOrderSubscriber();
        $this->locator->registerSubscriberService('domain.subscriber', PurchaseOrderSubscriber::class);
        $this->container
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with('domain.subscriber')
            ->will($this->returnValue($subscriber))
        ;

        $listeners = $this->locator->listenersOfEvent(new PurchaseOrderCompletedEvent());
        $expected = [
            [$subscriber, 'onCompleted1'],
            [$subscriber, 'onCompleted2'],
        ];
        $this->assertEquals($expected, $listeners);

        $listeners = $this->locator->listenersOfEvent(new PurchaseOrderCreatedEvent());
        $expected = [
            [$subscriber, 'onCreated'],
        ];
        $this->assertEquals($expected, $listeners);
    }
}
