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
use GpsLab\Domain\Event\Bus\EventBus;
use GpsLab\Domain\Event\Bus\QueueEventBus;
use GpsLab\Domain\Event\Event;
use GpsLab\Domain\Event\Queue\EventQueue;

class QueueEventBusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|EventQueue
     */
    private $queue;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|EventBus
     */
    private $publisher_bus;

    /**
     * @var QueueEventBus
     */
    private $bus;

    protected function setUp()
    {
        $this->queue = $this->getMock(EventQueue::class);
        $this->publisher_bus = $this->getMock(EventBus::class);

        $this->bus = new QueueEventBus($this->queue, $this->publisher_bus);

        parent::setUp();
    }

    public function testPublish()
    {
        /* @var $event \PHPUnit_Framework_MockObject_MockObject|Event */
        $event = $this->getMock(Event::class);

        $this->queue
            ->expects($this->once())
            ->method('push')
            ->with($event)
        ;

        $this->bus->publish($event);
    }

    public function testPullAndPublish()
    {
        /* @var $event1 \PHPUnit_Framework_MockObject_MockObject|Event */
        $event1 = $this->getMock(Event::class);
        /* @var $event2 \PHPUnit_Framework_MockObject_MockObject|Event */
        $event2 = $this->getMock(Event::class);

        /* @var $aggregator \PHPUnit_Framework_MockObject_MockObject|AggregateEvents */
        $aggregator = $this->getMock(AggregateEvents::class);
        $aggregator
            ->expects($this->atLeastOnce())
            ->method('pullEvents')
            ->will($this->returnValue([$event1, $event2]))
        ;

        $this->queue
            ->expects($this->at(0))
            ->method('push')
            ->with($event1)
        ;
        $this->queue
            ->expects($this->at(1))
            ->method('push')
            ->with($event2)
        ;

        $this->bus->pullAndPublish($aggregator);
    }
}
