<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Tests\Queue\Pull;

use GpsLab\Domain\Event\Queue\Pull\MemoryPullEventQueue;
use GpsLab\Domain\Event\Tests\Fixture\PurchaseOrderCompletedEvent;
use GpsLab\Domain\Event\Tests\Fixture\PurchaseOrderCreatedEvent;
use PHPUnit\Framework\TestCase;

class MemoryPullEventQueueTest extends TestCase
{
    /**
     * @var MemoryPullEventQueue
     */
    private $queue;

    protected function setUp()
    {
        $this->queue = new MemoryPullEventQueue();
    }

    public function testQueue()
    {
        $queue = [
            new PurchaseOrderCompletedEvent(),
            new PurchaseOrderCreatedEvent(),
            new PurchaseOrderCompletedEvent(), // duplicate
        ];

        foreach ($queue as $event) {
            $this->assertTrue($this->queue->publish($event));
        }

        $expected = array_reverse($queue);
        $i = count($expected);
        while ($event = $this->queue->pull()) {
            $this->assertEquals($expected[--$i], $event);
        }

        $this->assertEquals(0, $i, 'Queue cleared');
        $this->assertNull($event, 'No events in queue');
    }
}
