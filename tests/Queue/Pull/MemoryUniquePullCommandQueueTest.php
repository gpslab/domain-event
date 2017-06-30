<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Tests\Queue\Pull;

use GpsLab\Domain\Event\Queue\Pull\MemoryUniquePullEventQueue;
use GpsLab\Domain\Event\Tests\Fixture\PurchaseOrderCompletedEvent;
use GpsLab\Domain\Event\Tests\Fixture\PurchaseOrderCreatedEvent;

class MemoryUniquePullEventQueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MemoryUniquePullEventQueue
     */
    private $queue;

    protected function setUp()
    {
        $this->queue = new MemoryUniquePullEventQueue();
    }

    public function testQueue()
    {
        $queue = [
            new PurchaseOrderCompletedEvent(),
            new PurchaseOrderCreatedEvent(),
            new PurchaseOrderCompletedEvent(), // duplicate
            new PurchaseOrderCreatedEvent(), // duplicate
            new PurchaseOrderCreatedEvent(), // duplicate
            new PurchaseOrderCompletedEvent(), // duplicate
        ];
        $expected = [
            new PurchaseOrderCompletedEvent(),
            new PurchaseOrderCreatedEvent(),
        ];

        foreach ($queue as $event) {
            $this->assertTrue($this->queue->publish($event));
        }

        $i = count($expected);
        while ($event = $this->queue->pull()) {
            $this->assertEquals($expected[--$i], $event);
        }

        $this->assertEquals(0, $i, 'Queue cleared');
        $this->assertNull($event, 'No events in queue');
    }
}
