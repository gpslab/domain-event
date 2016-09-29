<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace GpsLab\Domain\Event\Tests\Listener\Locator;

use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\Listener\ListenerCollection;
use GpsLab\Domain\Event\Listener\ListenerInterface;
use GpsLab\Domain\Event\Listener\Locator\EventClassLocator;

class EventClassLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventClassLocator
     */
    private $locator;

    protected function setUp()
    {
        $this->locator = new EventClassLocator();
    }

    public function testRegister()
    {
        /* @var $event EventInterface */
        $event = $this->getMock(EventInterface::class);

        /* @var $listener1 ListenerInterface */
        $listener1 = $this->getMock(ListenerInterface::class);
        $this->locator->register(\stdClass::class, $listener1);

        /* @var $listener2 ListenerInterface */
        $listener2 = $this->getMock(ListenerInterface::class);
        $this->locator->register(\stdClass::class, $listener2);

        /* @var $listener3 ListenerInterface */
        $listener3 = $this->getMock(ListenerInterface::class);
        $this->locator->register(get_class($event), $listener3);

        /* @var $listener4 ListenerInterface */
        $listener4 = $this->getMock(ListenerInterface::class);
        $this->locator->register(get_class($event), $listener4);

        // test get list event listeners
        $this->assertInstanceOf(ListenerCollection::class, $this->locator->getRegisteredEventListeners());
        $this->assertEquals(
            new ListenerCollection([$listener1, $listener2, $listener3, $listener4]),
            $this->locator->getRegisteredEventListeners()
        );

        // test get event listeners for event
        $this->assertInstanceOf(ListenerCollection::class, $this->locator->getListenersForEvent($event));
        $this->assertEquals(
            new ListenerCollection([$listener3, $listener4]),
            $this->locator->getListenersForEvent($event)
        );
    }
}
