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
use GpsLab\Domain\Event\Bus\EventBus;
use GpsLab\Domain\Event\Bus\QueueEventBus;
use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\Queue\EventQueueInterface;

class QueueEventBusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|EventQueueInterface
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
        $this->queue = $this->getMock(EventQueueInterface::class);
        $this->publisher_bus = $this->getMock(EventBus::class);

        $this->bus = new QueueEventBus($this->queue, $this->publisher_bus);

        parent::setUp();
    }

    public function testPublish()
    {
        /* @var $event \PHPUnit_Framework_MockObject_MockObject|EventInterface */
        $event = $this->getMock(EventInterface::class);

        $this->queue
            ->expects($this->once())
            ->method('push')
            ->with($event)
        ;

        $this->bus->publish($event);
    }

    public function testPullAndPublish()
    {
        /* @var $event1 \PHPUnit_Framework_MockObject_MockObject|EventInterface */
        $event1 = $this->getMock(EventInterface::class);
        /* @var $event2 \PHPUnit_Framework_MockObject_MockObject|EventInterface */
        $event2 = $this->getMock(EventInterface::class);

        /* @var $aggregator \PHPUnit_Framework_MockObject_MockObject|AggregateEventsInterface */
        $aggregator = $this->getMock(AggregateEventsInterface::class);
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

    public function testPublishFromQueue()
    {
        /* @var $event \PHPUnit_Framework_MockObject_MockObject|EventInterface */
        $event = $this->getMock(EventInterface::class);

        $this->queue
            ->expects($this->at(0))
            ->method('pop')
            ->will($this->returnValue($event))
        ;
        $this->queue
            ->expects($this->at(1))
            ->method('pop')
            ->will($this->returnValue(null))
        ;

        $this->publisher_bus
            ->expects($this->once())
            ->method('publish')
            ->with($event)
        ;

        $this->bus->publishFromQueue();
    }

    public function testGetRegisteredEventListeners()
    {
        $expected = ['foo'];

        $this->publisher_bus
            ->expects($this->once())
            ->method('getRegisteredEventListeners')
            ->will($this->returnValue($expected))
        ;

        $this->assertEquals($expected, $this->bus->getRegisteredEventListeners());
    }
}
