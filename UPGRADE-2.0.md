UPGRADE FROM 1.x to 2.0
=======================

AggregateEventsRaiseInSelf
--------------------------

 * Not use `NameResolver` for get the handle method name from event for handle it.

PredisEventQueue
----------------

 * Available change queue name in Redis.
 * Available change serialize format.
 * Change used method of Serializer from `normalize()` to `serialize()`.
 * Change used method of Serializer from `denormalize()` to `deserialize()`.
 * Use interface `Symfony\Component\Serializer\SerializerInterface`.
   Not a `Symfony\Component\Serializer\Serializer`.

PredisUniqueEventQueue
----------------------

 * Available change queue name in Redis.
 * Available change serialize format.
 * Change used method of Serializer from `normalize()` to `serialize()`.
 * Change used method of Serializer from `denormalize()` to `deserialize()`.
 * Use interface `Symfony\Component\Serializer\SerializerInterface`. Not a `Symfony\Component\Serializer\Serializer`.

MemoryUniqueEventQueue
----------------------

 * Remove exists event from Memory queue and push it again (not override it). That is, the position of the event in the
   queue is changed.

DirectBindingEventListenerLocator
----------------------------

 * Not used the event name resolver for find listeners of event.

SymfonyContainerEventListenerLocator
----------------------------

 * Implements interface `Symfony\Component\DependencyInjection\ContainerAwareInterface`.
 * Not used the event name resolver for find listeners of event

Renamed classes
------------------

 * The `GpsLab\Domain\Event\Listener\Locator\ContainerAwareLocator` renamed to
   `GpsLab\Domain\Event\Listener\Locator\SymfonyContainerEventListenerLocator`.
 * The `GpsLab\Domain\Event\Listener\Locator\NamedEventLocator` renamed to
   `GpsLab\Domain\Event\Listener\Locator\DirectBindingEventListenerLocator`.

Renamed interfaces
------------------

 * The `GpsLab\Domain\Event\Bus\EventBus` renamed to `GpsLab\Domain\Event\Bus\HandlerLocatedEventBus`.
 * The `GpsLab\Domain\Event\Bus\EventBusInterface` renamed to `GpsLab\Domain\Event\Bus\EventBus`.
 * The `GpsLab\Domain\Event\Aggregator\AggregateEventsInterface` renamed to
   `GpsLab\Domain\Event\Aggregator\AggregateEvents`.
 * The `GpsLab\Domain\Event\EventInterface` renamed to `GpsLab\Domain\Event\Event`.
 * The `GpsLab\Domain\Event\Listener\Locator\EventListenerLocator` renamed to
   `GpsLab\Domain\Event\Listener\Locator\Locator`.
 * The `GpsLab\Domain\Event\Queue\EventQueueInterface` renamed to `GpsLab\Domain\Event\Queue\EventQueue`.

Removed classes and interfaces
------------------------------

 * The `GpsLab\Domain\Event\Bus\Bus` has been removed.
   Use the `GpsLab\Domain\Event\Bus\HandlerLocatedEventBus` class instead.
 * The `GpsLab\Domain\Event\Bus\BusInterface` interface has been removed.
   Use the `GpsLab\Domain\Event\Bus\EventBus` interface instead.
 * The `GpsLab\Domain\Event\NamedEventInterface` interface has been removed.
 * The `GpsLab\Domain\Event\NameResolver\NamedEventResolver` class has been removed.
 * The `GpsLab\Domain\Event\Exception\InvalidEventException` class has been removed.
 * The `GpsLab\Domain\Event\Listener\AbstractSwitchListener` class has been removed.
 * The `GpsLab\Domain\Event\Listener\SwitchListenerTrait` trait has been removed.
 * The `GpsLab\Domain\Event\Listener\Locator\VoterLocator` class has been removed.
 * The `GpsLab\Domain\Event\Listener\VoterListenerInterface` interface has been removed.

Renamed methods
------------------

 * The `GpsLab\Domain\Event\Queue\EventQueue::push()` renamed to `GpsLab\Domain\Event\Queue\EventQueue::publish()`.
 * The `GpsLab\Domain\Event\Queue\MemoryEventQueue::push()` renamed to
   `GpsLab\Domain\Event\Queue\MemoryEventQueue::publish()`.
 * The `GpsLab\Domain\Event\Queue\MemoryUniqueEventQueue::push()` renamed to
   `GpsLab\Domain\Event\Queue\MemoryUniqueEventQueue::publish()`.
 * The `GpsLab\Domain\Event\Queue\PredisEventQueue::push()` renamed to
   `GpsLab\Domain\Event\Queue\PredisEventQueue::publish()`.
 * The `GpsLab\Domain\Event\Queue\PredisUniqueEventQueue::push()` renamed to
   `GpsLab\Domain\Event\Queue\PredisUniqueEventQueue::publish()`.
 * The `GpsLab\Domain\Event\Listener\Locator\EventListenerLocator::getListenersForEvent()` renamed to
   `GpsLab\Domain\Event\Listener\Locator\EventListenerLocator::listenersOfEvent()`.
 * The `GpsLab\Domain\Event\Listener\Locator\ContainerAwareLocator::getListenersForEvent()` renamed to
   `GpsLab\Domain\Event\Listener\Locator\ContainerAwareLocator::listenersOfEvent()`.
 * The `GpsLab\Domain\Event\Listener\Locator\NamedEventLocator::getListenersForEvent()` renamed to
   `GpsLab\Domain\Event\Listener\Locator\NamedEventLocator::listenersOfEvent()`.

Removed methods
---------------

 * The `GpsLab\Domain\Event\Bus\EventBus::getRegisteredEventListeners()` has been removed.
 * The `GpsLab\Domain\Event\Bus\HandlerLocatedEventBus::getRegisteredEventListeners()` has been removed.
 * The `GpsLab\Domain\Event\Bus\QueueEventBus::getRegisteredEventListeners()` has been removed.
 * The `GpsLab\Domain\Event\Bus\QueueEventBus::publishFromQueue()` has been removed.
 * The `GpsLab\Domain\Event\Listener\Locator\Locator::getRegisteredEventListeners()` has been removed.
 * The `GpsLab\Domain\Event\Listener\Locator\ContainerAwareLocator::getRegisteredEventListeners()` has been removed.
 * The `GpsLab\Domain\Event\Listener\Locator\NamedEventLocator::getRegisteredEventListeners()` has been removed.

Renamed constants
-----------------

 * The `GpsLab\Domain\Event\Queue\PredisEventQueue::FORMAT` renamed to
   `GpsLab\Domain\Event\Queue\PredisEventQueue::DEFAULT_FORMAT`.
 * The `GpsLab\Domain\Event\Queue\PredisUniqueEventQueue::FORMAT` renamed to
   `GpsLab\Domain\Event\Queue\PredisUniqueEventQueue::DEFAULT_FORMAT`.

Removed constants
-----------------

 * The `GpsLab\Domain\Event\Queue\PredisEventQueue::LIST_KEY` has been removed.
 * The `GpsLab\Domain\Event\Queue\PredisUniqueEventQueue::SET_KEY` has been removed.
