<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace GpsLab\Domain\Event\Tests\NameResolver;

use GpsLab\Domain\Event\NameResolver\EventClassLastPartResolver;
use GpsLab\Domain\Event\Tests\Event\PurchaseOrderCreatedEvent;

class EventClassLastPartResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventClassLastPartResolver
     */
    private $resolver;

    protected function setUp()
    {
        $this->resolver = new EventClassLastPartResolver();
    }

    public function testGetEventNameInNamespace()
    {
        $this->assertEquals(
            'PurchaseOrderCreated',
            $this->resolver->getEventName(new PurchaseOrderCreatedEvent())
        );
    }

    public function testGetEventNameNoInNamespace()
    {
        $this->assertEquals(
            'PurchaseOrderCreated',
            $this->resolver->getEventName(new \Acme_Demo_PurchaseOrderCreated())
        );
    }
}
