<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Listener;

use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\NameResolver\EventClassLastPartResolver;
use GpsLab\Domain\Event\NameResolver\EventNameResolverInterface;

trait SwitchListenerTrait
{
    /**
     * @var EventNameResolverInterface
     */
    private $resolver;

    /**
     * @param EventNameResolverInterface $resolver
     */
    protected function changeEventNameResolver(EventNameResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @return EventNameResolverInterface
     */
    private function getEventNameResolver()
    {
        if (!($this->resolver instanceof EventNameResolverInterface)) {
            $this->resolver = new EventClassLastPartResolver(); // default name resolver
        }

        return $this->resolver;
    }

    /**
     * @param EventInterface $event
     */
    public function handle(EventInterface $event)
    {
        $event_name = $this->getEventNameResolver()->getEventName($event);
        $method = sprintf('handle%s', $event_name);

        if (method_exists($this, $method)) {
            call_user_func([$this, $method], $event);
        }
    }
}
