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
use GpsLab\Domain\Event\Queue\MemoryEventQueue;

class MemoryEventQueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MemoryEventQueue
     */
    private $queue;

    protected function setUp()
    {
        $this->queue = new MemoryEventQueue();
        parent::setUp();
    }

    public function testQueue()
    {
        /* @var $event1 \PHPUnit_Framework_MockObject_MockObject|EventInterface */
        $event1 = $this->getMock(EventInterface::class);
        /* @var $event2 \PHPUnit_Framework_MockObject_MockObject|EventInterface */
        $event2 = $this->getMock(EventInterface::class);

        $this->assertTrue($this->queue->push($event1));
        $this->assertTrue($this->queue->push($event2));
        $this->assertEquals($event1, $this->queue->pop());
        $this->assertEquals($event2, $this->queue->pop());
        $this->assertNull($this->queue->pop());
    }
}
