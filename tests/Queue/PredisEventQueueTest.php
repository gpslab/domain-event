<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Tests\Queue;

use GpsLab\Domain\Event\Event;
use GpsLab\Domain\Event\Queue\PredisEventQueue;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class PredisEventQueueTest extends \PHPUnit_Framework_TestCase
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

        parent::setUp();
    }

    /**
     * @param string $format
     *
     * @return PredisEventQueue
     */
    private function queue($format)
    {
        return new PredisEventQueue($this->client, $this->serializer, $this->logger, $this->queue_name, $format);
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
    public function testPush($format, $expected_format)
    {
        /* @var $event \PHPUnit_Framework_MockObject_MockObject|Event */
        $event = $this->getMock(Event::class);

        $normalize = 'foo';

        $this->serializer
            ->expects($this->once())
            ->method('serialize')
            ->with($event, $expected_format)
            ->will($this->returnValue($normalize))
        ;

        $this->client
            ->expects($this->once())
            ->method('__call')
            ->with('rpush', [$this->queue_name, [$normalize]])
            ->will($this->returnValue(1))
        ;

        $this->assertTrue($this->queue($format)->push($event));
    }

    /**
     * @dataProvider formats
     *
     * @param string $format
     */
    public function testPopIsEmptyQueue($format)
    {
        $this->client
            ->expects($this->once())
            ->method('__call')
            ->with('lpop', [$this->queue_name])
            ->will($this->returnValue(null))
        ;

        $this->serializer
            ->expects($this->never())
            ->method('deserialize')
        ;

        $this->logger
            ->expects($this->never())
            ->method('critical')
        ;

        $this->assertNull($this->queue($format)->pop());
    }

    /**
     * @dataProvider formats
     *
     * @param string $format
     * @param string $expected_format
     */
    public function testPop($format, $expected_format)
    {
        /* @var $event \PHPUnit_Framework_MockObject_MockObject|Event */
        $event = $this->getMock(Event::class);

        $normalize = 'foo';

        $this->client
            ->expects($this->once())
            ->method('__call')
            ->with('lpop', [$this->queue_name])
            ->will($this->returnValue($normalize))
        ;

        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with($normalize, Event::class, $expected_format)
            ->will($this->returnValue($event))
        ;

        $this->logger
            ->expects($this->never())
            ->method('critical')
        ;

        $this->assertEquals($event, $this->queue($format)->pop());
    }

    /**
     * @dataProvider formats
     *
     * @param string $format
     */
    public function testPopFailedDenormalize($format)
    {
        $normalize = 'foo';
        $message = 'bar';

        $this->client
            ->expects($this->at(0))
            ->method('__call')
            ->with('lpop', [$this->queue_name])
            ->will($this->returnValue($normalize))
        ;
        $this->client
            ->expects($this->at(1))
            ->method('__call')
            ->with('rpush', [$this->queue_name, [$normalize]])
        ;

        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->will($this->throwException(new \Exception($message)))
        ;

        $this->logger
            ->expects($this->once())
            ->method('critical')
            ->with(
                'Failed deserialize a event in the Redis queue',
                [
                    $normalize,
                    $message,
                ]
            )
        ;

        $this->assertNull($this->queue($format)->pop());
    }
}
