<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Tests\Queue;

use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\Queue\PredisEventQueue;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Serializer;

class PredisEventQueueTest extends \PHPUnit_Framework_TestCase
{
    const SET_KEY = 'events';
    const FORMAT = 'predis';

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Client
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
     * @var PredisEventQueue
     */
    private $queue;

    protected function setUp()
    {
        $this->client = $this->getMock(Client::class);
        $this->serializer = $this->getMock(Serializer::class);
        $this->logger = $this->getMock(LoggerInterface::class);

        $this->queue = new PredisEventQueue($this->client, $this->serializer, $this->logger);

        parent::setUp();
    }

    public function testPush()
    {
        /* @var $event \PHPUnit_Framework_MockObject_MockObject|EventInterface */
        $event = $this->getMock(EventInterface::class);

        $normalize = 'foo';

        $this->serializer
            ->expects($this->once())
            ->method('normalize')
            ->with($event, self::FORMAT)
            ->will($this->returnValue($normalize))
        ;

        $this->client
            ->expects($this->once())
            ->method('__call')
            ->with('lpush', [self::SET_KEY, [$normalize]])
            ->will($this->returnValue(1))
        ;

        $this->assertTrue($this->queue->push($event));
    }

    public function testPopIsEmptyQueue()
    {
        $this->client
            ->expects($this->once())
            ->method('__call')
            ->with('lpop', [self::SET_KEY])
            ->will($this->returnValue(null))
        ;

        $this->serializer
            ->expects($this->never())
            ->method('denormalize')
        ;

        $this->logger
            ->expects($this->never())
            ->method('critical')
        ;

        $this->assertNull($this->queue->pop());
    }

    public function testPop()
    {
        /* @var $event \PHPUnit_Framework_MockObject_MockObject|EventInterface */
        $event = $this->getMock(EventInterface::class);

        $normalize = 'foo';

        $this->client
            ->expects($this->once())
            ->method('__call')
            ->with('lpop', [self::SET_KEY])
            ->will($this->returnValue($normalize))
        ;

        $this->serializer
            ->expects($this->once())
            ->method('denormalize')
            ->with($normalize, EventInterface::class, self::FORMAT)
            ->will($this->returnValue($event))
        ;

        $this->logger
            ->expects($this->never())
            ->method('critical')
        ;

        $this->assertEquals($event, $this->queue->pop());
    }

    public function testPopFailedDenormalize()
    {
        $normalize = 'foo';
        $message = 'bar';

        $this->client
            ->expects($this->at(0))
            ->method('__call')
            ->with('lpop', [self::SET_KEY])
            ->will($this->returnValue($normalize))
        ;
        $this->client
            ->expects($this->at(1))
            ->method('__call')
            ->with('rpush', [self::SET_KEY, [$normalize]])
        ;

        $this->serializer
            ->expects($this->once())
            ->method('denormalize')
            ->will($this->throwException(new \Exception($message)))
        ;

        $this->logger
            ->expects($this->once())
            ->method('critical')
            ->with(
                'Failed denormalize a event in the Redis queue',
                [
                    $normalize,
                    $message,
                ]
            )
        ;

        $this->assertNull($this->queue->pop());
    }
}
