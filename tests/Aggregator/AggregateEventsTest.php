<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Tests\Aggregator;

use GpsLab\Domain\Event\Event;

class AggregateEventsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DemoAggregator
     */
    private $aggregator;

    protected function setUp()
    {
        $this->aggregator = new DemoAggregator();
    }

    public function testRaiseAndPullEvents()
    {
        $this->assertEquals([], $this->aggregator->pullEvents());

        $events = [
            $this->getMock(Event::class),
            $this->getMock(Event::class),
        ];

        foreach ($events as $event) {
            $this->aggregator->raiseEvent($event);
        }

        $this->assertEquals($events, $this->aggregator->pullEvents());
        $this->assertEquals([], $this->aggregator->pullEvents());
    }
}
