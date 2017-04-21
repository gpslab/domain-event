<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Tests\Queue;

use GpsLab\Domain\Event\Queue\MemoryUniqueEventQueue;
use GpsLab\Domain\Event\Tests\Event\PurchaseOrderCreatedEvent;

class MemoryUniqueEventQueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MemoryUniqueEventQueue
     */
    private $queue;

    protected function setUp()
    {
        $this->queue = new MemoryUniqueEventQueue();
        parent::setUp();
    }

    public function testQueue()
    {
        $event1 = new \Acme_Demo_PurchaseOrderCreated();
        $event2 = new PurchaseOrderCreatedEvent();

        $this->assertTrue($this->queue->push($event1));
        $this->assertFalse($this->queue->push($event1));
        $this->assertTrue($this->queue->push($event2));
        $this->assertFalse($this->queue->push($event2));
        $this->assertEquals($event2, $this->queue->pop());
        $this->assertEquals($event1, $this->queue->pop());
        $this->assertNull($this->queue->pop());
    }
}