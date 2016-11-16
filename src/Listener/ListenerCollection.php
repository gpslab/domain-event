<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace GpsLab\Domain\Event\Listener;

class ListenerCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var ListenerInterface[]
     */
    private $listeners = [];

    /**
     * @param ListenerInterface[] $listeners
     */
    public function __construct(array $listeners = [])
    {
        foreach ($listeners as $listener) {
            $this->add($listener);
        }
    }

    /**
     * @return ListenerInterface[]|\ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->listeners);
    }

    /**
     * @param ListenerInterface $listener
     */
    public function add(ListenerInterface $listener)
    {
        $this->listeners[] = $listener;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->listeners);
    }
}
