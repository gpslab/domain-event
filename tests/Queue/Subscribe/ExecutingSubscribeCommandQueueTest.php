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
use GpsLab\Domain\Event\Queue\Subscribe\ExecutingSubscribeEventQueue;

class ExecutingSubscribeEventQueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Event
     */
    private $event;

    /**
     * @var ExecutingSubscribeEventQueue
     */
    private $queue;

    protected function setUp()
    {
        $this->event = $this->getMock(Event::class);
        $this->queue = new ExecutingSubscribeEventQueue();
    }

    public function testPublish()
    {
        $subscriber_called = false;

        $this->queue->subscribe(function ($event) use (&$subscriber_called) {
            $this->assertInstanceOf(Event::class, $event);
            $this->assertEquals($this->event, $event);
            $subscriber_called = true;
        });

        $this->assertTrue($this->queue->publish($this->event));
        $this->assertTrue($subscriber_called);
    }
}
