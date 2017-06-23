<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Tests\Bus;

use GpsLab\Domain\Event\Aggregator\AggregateEventsInterface;
use GpsLab\Domain\Event\Bus\HandlerLocatedEventBus;
use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\Listener\ListenerInterface;
use GpsLab\Domain\Event\Listener\Locator\LocatorInterface;

class HandlerLocatedEventBusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|LocatorInterface
     */
    private $locator;

    /**
     * @var HandlerLocatedEventBus
     */
    private $bus;

    protected function setUp()
    {
        $this->locator = $this->getMock(LocatorInterface::class);
        $this->bus = new HandlerLocatedEventBus($this->locator);
    }

    public function testPublish()
    {
        /* @var $event \PHPUnit_Framework_MockObject_MockObject|EventInterface */
        $event = $this->getMock(EventInterface::class);

        /* @var $listeners \PHPUnit_Framework_MockObject_MockObject[] */
        $listeners = [
            $this->getMock(ListenerInterface::class),
            $this->getMock(ListenerInterface::class),
        ];

        foreach ($listeners as $listener) {
            $listener
                ->expects($this->once())
                ->method('handle')
                ->with($event);
        }

        $this->locator
            ->expects($this->once())
            ->method('getListenersForEvent')
            ->with($event)
            ->will($this->returnValue($listeners));

        $this->bus->publish($event);
    }

    public function testPullAndPublish()
    {
        /* @var $events \PHPUnit_Framework_MockObject_MockObject[] */
        $events = [
            $this->getMock(EventInterface::class),
            $this->getMock(EventInterface::class),
        ];

        foreach ($events as $i => $event) {
            /* @var $listeners \PHPUnit_Framework_MockObject_MockObject[] */
            $listeners = [
                $this->getMock(ListenerInterface::class),
                $this->getMock(ListenerInterface::class),
            ];

            foreach ($listeners as $listener) {
                $listener
                    ->expects($this->once())
                    ->method('handle')
                    ->with($event);
            }

            $this->locator
                ->expects($this->at($i))
                ->method('getListenersForEvent')
                ->with($event)
                ->will($this->returnValue($listeners));
        }

        /* @var $aggregator \PHPUnit_Framework_MockObject_MockObject|AggregateEventsInterface */
        $aggregator = $this->getMock(AggregateEventsInterface::class);
        $aggregator
            ->expects($this->once())
            ->method('pullEvents')
            ->will($this->returnValue($events));

        $this->bus->pullAndPublish($aggregator);
    }

    public function testGetRegisteredEventListeners()
    {
        $listeners = [
            $this->getMock(ListenerInterface::class),
        ];

        $this->locator
            ->expects($this->once())
            ->method('getRegisteredEventListeners')
            ->will($this->returnValue($listeners));

        $this->assertEquals($listeners, $this->bus->getRegisteredEventListeners());
    }
}
