<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace GpsLab\Domain\Event\Tests\Listener;

use GpsLab\Domain\Event\Listener\ListenerCollection;
use GpsLab\Domain\Event\Listener\ListenerInterface;

class ListenerCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ListenerCollection
     */
    private $collection;

    protected function setUp()
    {
        $this->collection = new ListenerCollection();
    }

    public function testTryAddBadListenersInConstruct()
    {
        if (version_compare(PHP_VERSION, '7.0.0', '>=')) {
            $this->markTestSkipped('Impossible test in PHP >= 7.0');
        } else {
            $this->setExpectedException(\PHPUnit_Framework_Error::class);

            new ListenerCollection([
                new \stdClass(),
            ]);
        }
    }

    public function testAddToCollection()
    {
        $this->assertInstanceOf(\IteratorAggregate::class, $this->collection);
        $this->assertInstanceOf(\Countable::class, $this->collection);

        $this->assertEquals(0, count($this->collection));

        $this->assertInstanceOf(\ArrayIterator::class, $this->collection->getIterator());

        /* @var $listener1 ListenerInterface */
        $listener1 = $this->getMock(ListenerInterface::class);

        $this->collection->add($listener1);

        $this->assertEquals(1, count($this->collection));
        $collection = $this->collection->getIterator();
        $this->assertEquals($listener1, reset($collection));

        /* @var $listener2 ListenerInterface */
        $listener2 = $this->getMock(ListenerInterface::class);
        $this->collection->add($listener2);

        $this->assertEquals(2, count($this->collection));
        $collection = $this->collection->getIterator();
        $this->assertEquals($listener1, reset($collection));
        $this->assertEquals($listener2, end($collection));
    }

    public function testNotEmptyConstruct()
    {
        $listener1 = $this->getMock(ListenerInterface::class);
        $listener2 = $this->getMock(ListenerInterface::class);

        $this->collection = new ListenerCollection([
            $listener1,
            $listener2,
        ]);

        $this->assertEquals(2, count($this->collection));
        $collection = $this->collection->getIterator();
        $this->assertEquals($listener1, reset($collection));
        $this->assertEquals($listener2, end($collection));
    }
}
