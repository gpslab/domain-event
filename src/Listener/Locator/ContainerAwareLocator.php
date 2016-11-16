<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace GpsLab\Domain\Event\Listener\Locator;

use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\Listener\ListenerCollection;
use GpsLab\Domain\Event\Listener\ListenerInterface;
use GpsLab\Domain\Event\NameResolver\EventNameResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerAwareLocator extends NamedEventLocator
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
     * @var array
     */
    private $listener_ids = [];

    /**
     * @var array
     */
    private $listeners = [];

    /**
     * @param EventNameResolverInterface $resolver
     * @param ContainerInterface $container
     */
    public function __construct(EventNameResolverInterface $resolver, ContainerInterface $container)
    {
        parent::__construct($resolver);
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
     * @param EventInterface $event
     *
     * @return ListenerInterface[]
     */
    public function getListenersForEvent(EventInterface $event)
    {
        $event_name = $this->resolver->getEventName($event);
        $this->lazyLoad($event_name);

        return parent::getListenersForEvent($event);
    }

    /**
     * @return ListenerCollection
     */
    public function getRegisteredEventListeners()
    {
        foreach ($this->listener_ids as $event_name => $service) {
            $this->lazyLoad($event_name);
        }

        return parent::getRegisteredEventListeners();
    }

    /**
     * @param string $event_name
     */
    protected function lazyLoad($event_name)
    {
        if (isset($this->listener_ids[$event_name])) {
            foreach ($this->listener_ids[$event_name] as $service_id) {
                $listener = $this->container->get($service_id);

                if ($listener instanceof ListenerInterface) {
                    if (!isset($this->listeners[$event_name][$service_id]) ||
                        $listener !== $this->listeners[$event_name][$service_id]
                    ) {
                        $this->register($event_name, $listener);
                        $this->listeners[$event_name][$service_id] = $listener;
                    }
                }
            }
        }
    }
}
