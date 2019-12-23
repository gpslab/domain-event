<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Tests\Bus;

use GpsLab\Domain\Event\Aggregator\AggregateEvents;
use GpsLab\Domain\Event\Bus\ListenerLocatedEventBus;
use GpsLab\Domain\Event\Listener\Locator\EventListenerLocator;
use GpsLab\Domain\Event\Tests\Fixture\Listener\PurchaseOrderCompletedEventListener;
use GpsLab\Domain\Event\Tests\Fixture\Listener\PurchaseOrderCreatedEventListener;
use GpsLab\Domain\Event\Tests\Fixture\PurchaseOrderCompletedEvent;
use GpsLab\Domain\Event\Tests\Fixture\PurchaseOrderCreatedEvent;
use PHPUnit\Framework\TestCase;

class ListenerLocatedEventBusTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|EventListenerLocator
     */
    private $locator;

    /**
     * @var ListenerLocatedEventBus
     */
    private $bus;

    protected function setUp()
    {
        $this->locator = $this->getMock(EventListenerLocator::class);
        $this->bus = new ListenerLocatedEventBus($this->locator);
    }

    public function testPublish()
    {
        $event = new PurchaseOrderCreatedEvent();
        $listener = new PurchaseOrderCreatedEventListener();
        $handled_event = null;

        $listeners = [
            function (PurchaseOrderCreatedEvent $event) use (&$handled_event) {
                $handled_event = $event;
            },
            $listener,
        ];

        $this->locator
            ->expects($this->once())
            ->method('listenersOfEvent')
            ->with($event)
            ->will($this->returnValue($listeners))
        ;

        $this->bus->publish($event);

        $this->assertEquals($event, $handled_event);
        $this->assertEquals($event, $listener->handledEvent());
    }

    public function testPullAndPublish()
    {
        /* @var $events \PHPUnit_Framework_MockObject_MockObject[] */
        $events = [
            $this->getMock(PurchaseOrderCompletedEvent::class),
            new PurchaseOrderCompletedEvent(),
        ];
        $handled_events = [];
        $listener = new PurchaseOrderCompletedEventListener();

        foreach ($events as $i => $event) {
            $listeners = [
                function (PurchaseOrderCompletedEvent $event) use (&$handled_events) {
                    $handled_events[] = $event;
                },
                [$listener, 'handle'],
            ];

            $this->locator
                ->expects($this->at($i))
                ->method('listenersOfEvent')
                ->with($event)
                ->will($this->returnValue($listeners))
            ;
        }

        /* @var $aggregator \PHPUnit_Framework_MockObject_MockObject|AggregateEvents */
        $aggregator = $this->getMock(AggregateEvents::class);
        $aggregator
            ->expects($this->once())
            ->method('pullEvents')
            ->will($this->returnValue($events))
        ;

        $this->bus->pullAndPublish($aggregator);

        $this->assertEquals($events, $handled_events);
        $this->assertEquals(end($events), $listener->handledEvent());
    }
}
