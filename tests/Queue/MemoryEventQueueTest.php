<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Tests\Queue;

use GpsLab\Domain\Event\Queue\MemoryEventQueue;
use GpsLab\Domain\Event\Tests\Event\PurchaseOrderCreatedEvent;

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
        $event1 = new \Acme_Demo_PurchaseOrderCreated();
        $event2 = new PurchaseOrderCreatedEvent();

        $this->assertTrue($this->queue->publish($event1));
        $this->assertTrue($this->queue->publish($event2));
        $this->assertEquals($event1, $this->queue->pop());
        $this->assertEquals($event2, $this->queue->pop());
        $this->assertNull($this->queue->pop());
    }
}
