<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Tests\Queue\Pull;

use GpsLab\Domain\Event\Event;
use GpsLab\Domain\Event\Queue\Pull\PredisPullEventQueue;
use GpsLab\Domain\Event\Tests\Fixture\PurchaseOrderCompletedEvent;
use GpsLab\Domain\Event\Tests\Fixture\PurchaseOrderCreatedEvent;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class PredisPullEventQueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Client
     */
    private $client;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|SerializerInterface
     */
    private $serializer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $queue_name = 'events';

    protected function setUp()
    {
        $this->client = $this->getMock(Client::class);
        $this->serializer = $this->getMock(SerializerInterface::class);
        $this->logger = $this->getMock(LoggerInterface::class);
    }

    /**
     * @param string $format
     *
     * @return PredisPullEventQueue
     */
    private function queue($format)
    {
        return new PredisPullEventQueue($this->client, $this->serializer, $this->logger, $this->queue_name, $format);
    }

    /**
     * @return array
     */
    public function formats()
    {
        return [
            [null, 'predis'],
            ['json', 'json'],
        ];
    }

    /**
     * @dataProvider formats
     *
     * @param string $format
     * @param string $expected_format
     */
    public function testPushQueue($format, $expected_format)
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
                ->with($event, $expected_format)
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
            $this->assertTrue($this->queue($format)->publish($event));
        }
    }

    /**
     * @dataProvider formats
     *
     * @param string $format
     * @param string $expected_format
     */
    public function testPopQueue($format, $expected_format)
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
                ->with($value, Event::class, $expected_format)
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
        while ($event = $this->queue($format)->pull()) {
            $this->assertEquals($expected[--$i], $event);
        }

        $this->assertEquals(0, $i, 'Queue cleared');
        $this->assertNull($event, 'No events in queue');
    }

    /**
     * @dataProvider formats
     *
     * @param string $format
     * @param string $expected_format
     */
    public function testFailedDeserialize($format, $expected_format)
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
            ->with($value, Event::class, $expected_format)
            ->will($this->throwException($exception))
        ;

        $this->logger
            ->expects($this->once())
            ->method('critical')
            ->with('Failed denormalize a event in the Redis queue', [$value, $exception->getMessage()])
            ->will($this->returnValue(1))
        ;

        $this->assertNull($this->queue($format)->pull());
    }
}
