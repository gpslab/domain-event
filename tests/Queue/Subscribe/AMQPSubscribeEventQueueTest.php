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
use GpsLab\Domain\Event\Queue\Subscribe\AMQPSubscribeEventQueue;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;

class AMQPSubscribeEventQueueTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Event
     */
    private $event;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AMQPChannel
     */
    private $channel;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Serializer
     */
    private $serializer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|LoggerInterface
     */
    private $logger;

    /**
     * @var AMQPSubscribeEventQueue
     */
    private $queue;

    /**
     * @var string
     */
    private $queue_name = 'events';

    protected function setUp()
    {
        $this->event = $this->getMock(Event::class);
        $this->serializer = $this->getMock(Serializer::class);
        $this->logger = $this->getMock(LoggerInterface::class);
        $this->channel = $this
            ->getMockBuilder(AMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->queue = new AMQPSubscribeEventQueue(
            $this->channel,
            $this->serializer,
            $this->logger,
            $this->queue_name
        );
    }

    public function testPublish()
    {
        $message = 'foo';

        $this->serializer
            ->expects($this->once())
            ->method('serialize')
            ->with($this->event)
            ->will($this->returnValue($message))
        ;

        $this->channel
            ->expects($this->once())
            ->method('queue_declare')
            ->with($this->queue_name, false, false, false, false)
        ;
        $this->channel
            ->expects($this->once())
            ->method('basic_publish')
            ->will($this->returnCallback(function ($msg, $exchange, $routing_key) use ($message) {
                $this->assertInstanceOf(AMQPMessage::class, $msg);
                $this->assertEquals($message, $msg->body);
                $this->assertEquals('', $exchange);
                $this->assertEquals($this->queue_name, $routing_key);
            }))
        ;

        $this->assertTrue($this->queue->publish($this->event));
    }

    /**
     * @throws \ErrorException
     */
    public function testSubscribe()
    {
        $subscriber_called = false;
        $handler = function ($event) use (&$subscriber_called) {
            $this->assertInstanceOf(Event::class, $event);
            $this->assertEquals($this->event, $event);
            $subscriber_called = true;
        };

        $this->channel
            ->expects($this->once())
            ->method('queue_declare')
            ->with($this->queue_name, false, false, false, false)
        ;
        $this->channel
            ->expects($this->once())
            ->method('basic_consume')
            ->will($this->returnCallback(function (
                $queue,
                $consumer_tag,
                $no_local,
                $no_ack,
                $exclusive,
                $nowait,
                $callback
            ) use ($handler) {
                $this->assertEquals($this->queue_name, $queue);
                $this->assertEquals('', $consumer_tag, 'consumer_tag must be empty');
                $this->assertFalse($no_local, 'no_local must be disabled');
                $this->assertTrue($no_ack, 'no_ack must be enabled');
                $this->assertFalse($exclusive, 'exclusive must be disabled');
                $this->assertFalse($nowait, 'nowait must be disabled');
                $this->assertTrue(is_callable($callback), 'callback must be callable');

                $message = 'foo';
                $this->serializer
                    ->expects($this->once())
                    ->method('deserialize')
                    ->with($message)
                    ->will($this->returnValue($this->event))
                ;

                call_user_func($callback, new AMQPMessage($message));
            }))
        ;
        $this->channel
            ->expects($this->at(2))
            ->method('is_consuming')
            ->will($this->returnValue(true))
        ;
        $this->channel
            ->expects($this->once())
            ->method('wait')
        ;
        $this->channel
            ->expects($this->at(3))
            ->method('is_consuming')
            ->will($this->returnValue(false))
        ;

        $this->queue->subscribe($handler);

        $this->assertTrue($subscriber_called, 'handler must be called');
    }

    /**
     * @throws \ErrorException
     */
    public function testSubscribeFailure()
    {
        $subscriber_called = false;
        $handler = function ($event) use (&$subscriber_called) {
            $this->assertInstanceOf(Event::class, $event);
            $this->assertEquals($this->event, $event);
            $subscriber_called = true;
        };

        $this->channel
            ->expects($this->once())
            ->method('queue_declare')
            ->with($this->queue_name, false, false, false, false)
        ;
        $this->channel
            ->expects($this->once())
            ->method('basic_consume')
            ->will($this->returnCallback(function (
                $queue,
                $consumer_tag,
                $no_local,
                $no_ack,
                $exclusive,
                $nowait,
                $callback
            ) use ($handler) {
                $this->assertEquals($this->queue_name, $queue);
                $this->assertEquals('', $consumer_tag, 'consumer_tag must be empty');
                $this->assertFalse($no_local, 'no_local must be disabled');
                $this->assertTrue($no_ack, 'no_ack must be enabled');
                $this->assertFalse($exclusive, 'exclusive must be disabled');
                $this->assertFalse($nowait, 'nowait must be disabled');
                $this->assertTrue(is_callable($callback), 'callback must be callable');

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
                    ->with('Failed denormalize a event in the AMQP queue', [$message, $exception->getMessage()])
                ;

                $this->channel
                    ->expects($this->once())
                    ->method('basic_publish')
                    ->will($this->returnCallback(function ($msg, $exchange, $routing_key) use ($message) {
                        $this->assertInstanceOf(AMQPMessage::class, $msg);
                        $this->assertEquals($message, $msg->body);
                        $this->assertEquals('', $exchange);
                        $this->assertEquals($this->queue_name, $routing_key);
                    }))
                ;

                call_user_func($callback, new AMQPMessage($message));
            }))
        ;
        $this->channel
            ->expects($this->once())
            ->method('is_consuming')
            ->will($this->returnValue(false))
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

        $this->channel
            ->expects($this->once())
            ->method('queue_declare')
            ->with($this->queue_name, false, false, false, false)
        ;
        $this->channel
            ->expects($this->once())
            ->method('basic_consume')
            ->will($this->returnCallback(function (
                $queue,
                $consumer_tag,
                $no_local,
                $no_ack,
                $exclusive,
                $nowait,
                $callback
            ) use ($handler) {
                $this->assertEquals($this->queue_name, $queue);
                $this->assertEquals('', $consumer_tag, 'consumer_tag must be empty');
                $this->assertFalse($no_local, 'no_local must be disabled');
                $this->assertTrue($no_ack, 'no_ack must be enabled');
                $this->assertFalse($exclusive, 'exclusive must be disabled');
                $this->assertFalse($nowait, 'nowait must be disabled');
                $this->assertTrue(is_callable($callback), 'callback must be callable');

                $message = 'foo';
                $this->serializer
                    ->expects($this->once())
                    ->method('deserialize')
                    ->with($message)
                    ->will($this->returnValue($this->event))
                ;

                call_user_func($callback, new AMQPMessage($message));
            }))
        ;
        $this->channel
            ->expects($this->never())
            ->method('is_consuming')
            ->will($this->returnValue(false))
        ;

        $this->queue->subscribe($handler);
    }

    /**
     * @throws \ErrorException
     */
    public function testLazeSubscribe()
    {
        $handler1 = function ($event) {
            $this->assertInstanceOf(Event::class, $event);
            $this->assertEquals($this->event, $event);
        };
        $handler2 = function (Event $event) {
        };

        $this->channel
            ->expects($this->once())
            ->method('queue_declare')
        ;
        $this->channel
            ->expects($this->once())
            ->method('basic_consume')
        ;
        $this->channel
            ->expects($this->exactly(2))
            ->method('is_consuming')
            ->will($this->returnValue(false))
        ;

        $this->assertFalse($this->queue->unsubscribe($handler1));
        $this->assertFalse($this->queue->unsubscribe($handler2));

        $this->queue->subscribe($handler1);

        $this->assertTrue($this->queue->unsubscribe($handler1));
        $this->assertFalse($this->queue->unsubscribe($handler1));

        $this->queue->subscribe($handler2);

        $this->assertTrue($this->queue->unsubscribe($handler2));
        $this->assertFalse($this->queue->unsubscribe($handler2));
    }
}
