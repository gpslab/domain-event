UPGRADE FROM 1.x to 2.0
=======================

Queue
-----

 * Unique queue has been removed.
 * Pull queues moved to `GpsLab\Domain\Event\Queue\Pull` namespace.
 * Created separate interface `GpsLab\Domain\Event\Queue\Pull\PullEventQueue` for poll queue.
 * Created subscribe queues and interface for it.
 * Created separate interface `GpsLab\Domain\Event\Queue\Subscribe\SubscribeEventQueue` for subscribe queue.

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

DirectBindingEventListenerLocator
---------------------------------

 * Not used the event name resolver for find listeners of event.
 * Use [callable type](http://php.net/manual/en/language.types.callable.php) as a event listener.

ContainerEventListenerLocator
-----------------------------

 * Created event listener locator that use
   [PSR-11](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-11-container.md) container.

SymfonyContainerEventListenerLocator
------------------------------------

 * Implements interface `Symfony\Component\DependencyInjection\ContainerAwareInterface`.
 * Not used the event name resolver for find listeners of event
 * Use [callable type](http://php.net/manual/en/language.types.callable.php) as a event listener.

Created classes and interfaces
------------------------------

* The `GpsLab\Domain\Event\Queue\Pull\PullEventQueue` interface has been created.
* The `GpsLab\Domain\Event\Queue\Subscribe\SubscribeEventQueue` interface has been created.
* The `GpsLab\Domain\Event\Queue\Subscribe\ExecutingSubscribeEventQueue` class has been created.
* The `GpsLab\Domain\Event\Queue\Subscribe\PredisSubscribeEventQueue` class has been created.

Renamed classes
---------------

 * The `GpsLab\Domain\Event\Listener\Locator\ContainerAwareLocator` renamed to
   `GpsLab\Domain\Event\Listener\Locator\SymfonyContainerEventListenerLocator`.
 * The `GpsLab\Domain\Event\Listener\Locator\NamedEventLocator` renamed to
   `GpsLab\Domain\Event\Listener\Locator\DirectBindingEventListenerLocator`.
 * The `GpsLab\Domain\Event\Queue\MemoryEventQueue` renamed to `GpsLab\Domain\Event\Queue\Pull\MemoryPullEventQueue`.
 * The `GpsLab\Domain\Event\Queue\PredisEventQueue` renamed to `GpsLab\Domain\Event\Queue\Pull\PredisPullEventQueue`.

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
 * The `GpsLab\Domain\Event\NameResolver\EventNameResolverInterface` interface has been removed.
 * The `GpsLab\Domain\Event\NameResolver\EventClassResolver` class has been removed.
 * The `GpsLab\Domain\Event\NameResolver\EventClassLastPartResolver` class has been removed.
 * The `GpsLab\Domain\Event\NameResolver\NameResolverContainer` class has been removed.
 * The `GpsLab\Domain\Event\Listener\ListenerCollection` class has been removed.
 * The `GpsLab\Domain\Event\Listener\ListenerInterface` interface has been removed.
 * The `GpsLab\Domain\Event\Queue\MemoryUniqueEventQueue` class has been removed.
 * The `GpsLab\Domain\Event\Queue\PredisUniqueEventQueue` class has been removed.

Renamed methods
---------------

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
 * The `GpsLab\Domain\Event\Listener\Locator\DirectBindingEventListenerLocator::getRegisteredEventListeners()` has been
   removed.
 * The `GpsLab\Domain\Event\Listener\Locator\SymfonyContainerEventListenerLocator::getRegisteredEventListeners()` has
   been removed.
 * The `GpsLab\Domain\Event\Listener\Locator\SymfonyContainerEventListenerLocator::register()` has been removed.
 * The `GpsLab\Domain\Event\Queue\EventQueue::pop()` has been removed.

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
