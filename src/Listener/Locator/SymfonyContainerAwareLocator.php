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
use GpsLab\Domain\Event\Listener\ListenerCollection;
use GpsLab\Domain\Event\Listener\ListenerInterface;
use GpsLab\Domain\Event\NameResolver\EventNameResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SymfonyContainerAwareLocator implements EventListenerLocator
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EventNameResolverInterface
     */
    private $resolver;

    /**
     * @var ListenerCollection[]
     */
    private $listeners = [];

    /**
     * @var array
     */
    private $listener_ids = [];

    /**
     * @var ListenerInterface[][]
     */
    private $listener_loaded = [];

    /**
     * @param EventNameResolverInterface $resolver
     * @param ContainerInterface         $container
     */
    public function __construct(EventNameResolverInterface $resolver, ContainerInterface $container)
    {
        $this->container = $container;
        $this->resolver = $resolver;
    }

    /**
     * @param string $event_name
     * @param string $service
     */
    public function registerService($event_name, $service)
    {
        $this->listener_ids[$event_name][] = $service;
    }

    /**
     * @param Event $event
     *
     * @return ListenerInterface[]|ListenerCollection
     */
    public function listenersOfEvent(Event $event)
    {
        $event_name = $this->resolver->getEventName($event);
        $this->lazyLoad($event_name);

        if (isset($this->listeners[$event_name])) {
            return $this->listeners[$event_name];
        } else {
            return new ListenerCollection();
        }
    }

    /**
     * @param string            $event_name
     * @param ListenerInterface $listener
     */
    public function register($event_name, ListenerInterface $listener)
    {
        if (!isset($this->listeners[$event_name])) {
            $this->listeners[$event_name] = new ListenerCollection();
        }

        $this->listeners[$event_name]->add($listener);
    }

    /**
     * @param string $event_name
     */
    protected function lazyLoad($event_name)
    {
        if (!isset($this->listener_ids[$event_name])) {
            return;
        }

        foreach ($this->listener_ids[$event_name] as $service_id) {
            $listener = $this->container->get($service_id);

            if ($listener instanceof ListenerInterface &&
                (
                    !isset($this->listener_loaded[$event_name][$service_id]) ||
                    $listener !== $this->listener_loaded[$event_name][$service_id]
                )
            ) {
                $this->listener_loaded[$event_name][$service_id] = $listener;

                // rebuild listener collection
                $this->listeners[$event_name] = new ListenerCollection(array_values(
                    $this->listener_loaded[$event_name]
                ));
            }
        }
    }
}
