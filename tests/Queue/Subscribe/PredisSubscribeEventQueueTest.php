<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Tests\Queue\Subscribe;

use GpsLab\Domain\Event\Event;
use GpsLab\Domain\Event\Queue\Serializer\Serializer;
use GpsLab\Domain\Event\Queue\Subscribe\PredisSubscribeEventQueue;
use Psr\Log\LoggerInterface;
use Superbalist\PubSub\Redis\RedisPubSubAdapter;

class PredisSubscribeEventQueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Event
     */
    private $event;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RedisPubSubAdapter
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
     * @var PredisSubscribeEventQueue
     */
    private $queue;

    /**
     * @var string
     */
    private $queue_name = 'events';

    protected function setUp()
    {
        if (!class_exists(RedisPubSubAdapter::class)) {
            $this->markTestSkipped('php-pubsub-redis is not installed.');
        }

        $this->event = $this->getMock(Event::class);
        $this->serializer = $this->getMock(Serializer::class);
        $this->logger = $this->getMock(LoggerInterface::class);
        $this->client = $this
            ->getMockBuilder(RedisPubSubAdapter::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->queue = new PredisSubscribeEventQueue(
            $this->client,
            $this->serializer,
            $this->logger,
            $this->queue_name
        );
    }

    public function testPublish()
    {
        $massage = 'foo';

        $this->serializer
            ->expects($this->once())
            ->method('serialize')
            ->with($this->event)
            ->will($this->returnValue($massage))
        ;

        $this->client
            ->expects($this->once())
            ->method('publish')
            ->with($this->queue_name, $massage)
        ;

        $this->assertTrue($this->queue->publish($this->event));
    }

    public function testSubscribe()
    {
        $subscriber_called = false;
        $handler = function ($event) use (&$subscriber_called) {
            $this->assertInstanceOf(Event::class, $event);
            $this->assertEquals($this->event, $event);
            $subscriber_called = true;
        };

        $this->client
            ->expects($this->once())
            ->method('subscribe')
            ->will($this->returnCallback(function ($queue_name, $handler_wrapper) use ($handler) {
                $this->assertEquals($this->queue_name, $queue_name);
                $this->assertTrue(is_callable($handler_wrapper));

                $message = 'foo';
                $this->serializer
                    ->expects($this->once())
                    ->method('deserialize')
                    ->with($message)
                    ->will($this->returnValue($this->event))
                ;

                call_user_func($handler_wrapper, $message);
            }))
        ;

        $this->queue->subscribe($handler);

        $this->assertTrue($subscriber_called);
    }

    public function testSubscribeFailure()
    {
        $subscriber_called = false;
        $handler = function ($event) use (&$subscriber_called) {
            $this->assertInstanceOf(Event::class, $event);
            $this->assertEquals($this->event, $event);
            $subscriber_called = true;
        };

        $this->client
            ->expects($this->once())
            ->method('subscribe')
            ->will($this->returnCallback(function ($queue_name, $handler_wrapper) use ($handler) {
                $this->assertEquals($this->queue_name, $queue_name);
                $this->assertTrue(is_callable($handler_wrapper));

                $exception = new \Exception('bar');
                $message = 'foo';
                $this->serializer
                    ->expects($this->once())
                    ->method('deserialize')
                    ->with($message)
                    ->will($this->throwException($exception))
                ;

                $this->logger
                    ->expects($this->once())
                    ->method('critical')
                    ->with(
                        'Failed denormalize a event in the Redis queue',
                        [$message, $exception->getMessage()]
                    )
                ;

                $this->client
                    ->expects($this->once())
                    ->method('publish')
                    ->with($this->queue_name, $message)
                ;

                call_user_func($handler_wrapper, $message);
            }))
        ;

        $this->queue->subscribe($handler);

        $this->assertFalse($subscriber_called);
    }

    /**
     * @expectedException \Exception
     */
    public function testSubscribeHandlerFailure()
    {
        $exception = new \Exception('bar');
        $handler = function ($event) use ($exception) {
            $this->assertInstanceOf(Event::class, $event);
            $this->assertEquals($this->event, $event);
            throw $exception;
        };

        $this->client
            ->expects($this->once())
            ->method('subscribe')
            ->will($this->returnCallback(function ($queue_name, $handler_wrapper) use ($handler) {
                $this->assertEquals($this->queue_name, $queue_name);
                $this->assertTrue(is_callable($handler_wrapper));

                $message = 'foo';
                $this->serializer
                    ->expects($this->once())
                    ->method('deserialize')
                    ->with($message)
                    ->will($this->returnValue($this->event))
                ;

                call_user_func($handler_wrapper, $message);
            }))
        ;

        $this->queue->subscribe($handler);
    }
}
