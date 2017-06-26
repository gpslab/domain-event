<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Tests\Listener\Locator;

use GpsLab\Domain\Event\Event;
use GpsLab\Domain\Event\Listener\ListenerCollection;
use GpsLab\Domain\Event\Listener\Locator\VoterLocator;
use GpsLab\Domain\Event\Listener\VoterListenerInterface;

class VoterLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var VoterLocator
     */
    private $locator;

    protected function setUp()
    {
        $this->locator = new VoterLocator();
    }

    public function testRegister()
    {
        /* @var $event Event */
        $event = $this->getMock(Event::class);

        /* @var $listener1 \PHPUnit_Framework_MockObject_MockObject|VoterListenerInterface */
        $listener1 = $this->getMock(VoterListenerInterface::class);
        $listener1
            ->expects($this->once())
            ->method('isSupportedEvent')
            ->with($event)
            ->will($this->returnValue(false));
        $this->locator->register($listener1);

        /* @var $listener2 \PHPUnit_Framework_MockObject_MockObject|VoterListenerInterface */
        $listener2 = $this->getMock(VoterListenerInterface::class);
        $listener2
            ->expects($this->once())
            ->method('isSupportedEvent')
            ->with($event)
            ->will($this->returnValue(true));
        $this->locator->register($listener2);

        // test get event listeners for event
        $collection = $this->locator->getListenersForEvent($event);
        $this->assertInstanceOf(ListenerCollection::class, $collection);
        $this->assertEquals(new ListenerCollection([$listener2]), $collection);
    }
}
