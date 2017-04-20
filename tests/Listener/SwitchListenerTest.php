<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Tests\Listener;

use GpsLab\Domain\Event\Tests\Event\PurchaseOrderCreatedEvent;

class SwitchListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testSwitch()
    {
        $event = new PurchaseOrderCreatedEvent();
        $listener = new DemoSwitchListener();
        $listener->handle($event);

        $this->assertEquals($event, $listener->getLastEvent());
    }
}
