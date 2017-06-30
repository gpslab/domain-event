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
use GpsLab\Domain\Event\Listener\Locator\DirectBindingEventListenerLocator;
use GpsLab\Domain\Event\Tests\Fixture\Listener\PurchaseOrderCompletedEventListener;
use GpsLab\Domain\Event\Tests\Fixture\Listener\PurchaseOrderCreatedEventListener;

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

        $listener1 = function (Event $event) {
        };
        $this->locator->register('foo', $listener1);

        $listener2 = new PurchaseOrderCreatedEventListener();
        $this->locator->register('foo', $listener2);

        $listener3 = function (Event $event) {
        };
        $this->locator->register(get_class($event), $listener3);

        $listener4 = [new PurchaseOrderCompletedEventListener(), 'handle'];
        $this->locator->register(get_class($event), $listener4);

        // test get event listeners for event
        $this->assertEquals([$listener3, $listener4], $this->locator->listenersOfEvent($event));
    }

    public function testRegisterNoListenersForEvent()
    {
        /* @var $event Event */
        $event = $this->getMock(Event::class);

        $listener1 = function (Event $event) {
        };
        $this->locator->register('foo', $listener1);

        $listener2 = new PurchaseOrderCreatedEventListener();
        $this->locator->register('foo', $listener2);

        $this->assertEquals([], $this->locator->listenersOfEvent($event));
    }
}
