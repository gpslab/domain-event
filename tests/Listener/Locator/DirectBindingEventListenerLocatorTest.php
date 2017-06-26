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
use GpsLab\Domain\Event\Listener\ListenerInterface;
use GpsLab\Domain\Event\Listener\Locator\DirectBindingEventListenerLocator;

class DirectBindingEventListenerLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DirectBindingEventListenerLocator
     */
    private $locator;

    protected function setUp()
    {
        $this->locator = new DirectBindingEventListenerLocator();
    }

    public function testRegister()
    {
        /* @var $event Event */
        $event = $this->getMock(Event::class);

        /* @var $listener1 ListenerInterface */
        $listener1 = $this->getMock(ListenerInterface::class);
        $this->locator->register('foo', $listener1);

        /* @var $listener2 ListenerInterface */
        $listener2 = $this->getMock(ListenerInterface::class);
        $this->locator->register('foo', $listener2);

        /* @var $listener3 ListenerInterface */
        $listener3 = $this->getMock(ListenerInterface::class);
        $this->locator->register(get_class($event), $listener3);

        /* @var $listener4 ListenerInterface */
        $listener4 = $this->getMock(ListenerInterface::class);
        $this->locator->register(get_class($event), $listener4);

        // test get event listeners for event
        $listeners = $this->locator->listenersOfEvent($event);
        $this->assertInstanceOf(ListenerCollection::class, $listeners);
        $this->assertEquals(new ListenerCollection([$listener3, $listener4]), $listeners);
    }

    public function testNoListenersForEvent()
    {
        /* @var $event Event */
        $event = $this->getMock(Event::class);

        /* @var $listener1 ListenerInterface */
        $listener1 = $this->getMock(ListenerInterface::class);
        $this->locator->register('foo', $listener1);

        /* @var $listener2 ListenerInterface */
        $listener2 = $this->getMock(ListenerInterface::class);
        $this->locator->register('foo', $listener2);

        $listeners = $this->locator->listenersOfEvent($event);
        $this->assertInstanceOf(ListenerCollection::class, $listeners);
        $this->assertEquals(new ListenerCollection(), $listeners);
    }
}
