<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Tests\Queue\Pull;

use GpsLab\Domain\Event\Queue\Pull\PredisPullEventQueue;
use GpsLab\Domain\Event\Queue\Serializer\Serializer;
use GpsLab\Domain\Event\Tests\Fixture\PurchaseOrderCompletedEvent;
use GpsLab\Domain\Event\Tests\Fixture\PurchaseOrderCreatedEvent;
use Predis\ClientInterface;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;

class PredisPullEventQueueTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ClientInterface
     */
    private $client;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Serializer
     */
    private $serializer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|LoggerInterface
     */
    private $logger;

    /**
     * @var PredisPullEventQueue
     */
    private $queue;

    /**
     * @var string
     */
    private $queue_name = 'events';

    protected function setUp()
    {
        $this->client = $this->getMock(ClientInterface::class);
        $this->serializer = $this->getMock(Serializer::class);
        $this->logger = $this->getMock(LoggerInterface::class);
        $this->queue = new PredisPullEventQueue($this->client, $this->serializer, $this->logger, $this->queue_name);
    }

    public function testPushQueue()
    {
        $queue = [
            new PurchaseOrderCreatedEvent(),
            new PurchaseOrderCompletedEvent(),
            new PurchaseOrderCreatedEvent(), // duplicate
        ];

        $i = 0;
        foreach ($queue as $event) {
            $value = $i.spl_object_hash($event);

            $this->serializer
                ->expects($this->at($i))
                ->method('serialize')
                ->with($event)
                ->will($this->returnValue($value))
            ;

            $this->client
                ->expects($this->at($i))
                ->method('__call')
                ->with('rpush', [$this->queue_name, [$value]])
                ->will($this->returnValue(1))
            ;
            ++$i;
        }

        foreach ($queue as $event) {
            $this->assertTrue($this->queue->publish($event));
        }
    }

    public function testPopQueue()
    {
        $queue = [
            new PurchaseOrderCreatedEvent(),
            new PurchaseOrderCompletedEvent(),
            new PurchaseOrderCreatedEvent(), // duplicate
        ];

        $i = 0;
        foreach ($queue as $event) {
            $value = $i.spl_object_hash($event);

            $this->serializer
                ->expects($this->at($i))
                ->method('deserialize')
                ->with($value)
                ->will($this->returnValue($event))
            ;

            $this->client
                ->expects($this->at($i))
                ->method('__call')
                ->with('lpop', [$this->queue_name])
                ->will($this->returnValue($value))
            ;
            ++$i;
        }
        $this->client
            ->expects($this->at($i))
            ->method('__call')
            ->with('lpop', [$this->queue_name])
            ->will($this->returnValue(null))
        ;

        $expected = array_reverse($queue);
        $i = count($expected);
        while ($event = $this->queue->pull()) {
            $this->assertEquals($expected[--$i], $event);
        }

        $this->assertEquals(0, $i, 'Queue cleared');
        $this->assertNull($event, 'No events in queue');
    }

    public function testFailedDeserialize()
    {
        $exception = new \Exception('foo');
        $event = new PurchaseOrderCreatedEvent();
        $value = spl_object_hash($event);

        $this->client
            ->expects($this->at(0))
            ->method('__call')
            ->with('lpop', [$this->queue_name])
            ->will($this->returnValue($value))
        ;
        $this->client
            ->expects($this->at(1))
            ->method('__call')
            ->with('rpush', [$this->queue_name, [$value]])
            ->will($this->returnValue(1))
        ;

        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with($value)
            ->will($this->throwException($exception))
        ;

        $this->logger
            ->expects($this->once())
            ->method('critical')
            ->with('Failed denormalize a event in the Redis queue', [$value, $exception->getMessage()])
            ->will($this->returnValue(1))
        ;

        $this->assertNull($this->queue->pull());
    }
}
