<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Listener\Locator;

use GpsLab\Domain\Event\Event;
use Psr\Container\ContainerInterface;

class ContainerEventListenerLocator implements EventListenerLocator
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var callable[][]
     */
    private $listeners = [];

    /**
     * @var array
     */
    private $listener_ids = [];

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Event $event
     *
     * @return callable[]
     */
    public function listenersOfEvent(Event $event)
    {
        $event_name = get_class($event);
        $this->lazyLoad($event_name);

        return array_values($this->listeners[$event_name]);
    }

    /**
     * @param string $event_name
     * @param string $service
     * @param string $method
     */
    public function registerService($event_name, $service, $method = '__invoke')
    {
        $this->listener_ids[$event_name][] = [$service, $method];
    }

    /**
     * @param string $event_name
     *
     * @return callable[]
     */
    private function lazyLoad($event_name)
    {
        if (!isset($this->listeners[$event_name])) {
            $this->listeners[$event_name] = [];
        }

        if (isset($this->listener_ids[$event_name])) {
            foreach ($this->listener_ids[$event_name] as $args) {
                list($service, $method) = $args;
                $listener = $this->resolve($this->container->get($service), $method);

                if ($listener) {
                    $this->listeners[$event_name][$service] = $listener;
                }
            }
        }
    }

    /**
     * @param mixed  $service
     * @param string $method
     *
     * @return callable|null
     */
    private function resolve($service, $method)
    {
        if (is_callable($service)) {
            return $service;
        }

        if (is_callable([$service, $method])) {
            return [$service, $method];
        }

        return null;
    }
}
