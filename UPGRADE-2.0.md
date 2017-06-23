UPGRADE FROM 1.x to 2.0
=======================

## AggregateEventsRaiseInSelf

* Not use `NameResolver` for get the handle method name from event for handle it.

## PredisEventQueue

* Available change queue name in Redis.
* Available change serialize format.
* Change used method of Serializer from `normalize()` to `serialize()`.
* Change used method of Serializer from `denormalize()` to `deserialize()`.
* Use interface `Symfony\Component\Serializer\SerializerInterface`. Not a `Symfony\Component\Serializer\Serializer`.

## MemoryUniqueEventQueue

* Available change queue name in Redis.
* Available change serialize format.
* Change used method of Serializer from `normalize()` to `serialize()`.
* Change used method of Serializer from `denormalize()` to `deserialize()`.
* Use interface `Symfony\Component\Serializer\SerializerInterface`. Not a `Symfony\Component\Serializer\Serializer`.
* Remove exists event from Memory queue and push it again (not override it). That is, the position of the event in the
queue is changed.

## Renamed interfaces

* The `GpsLab\Domain\Event\Bus\EventBus` renamed to `GpsLab\Domain\Event\Bus\HandlerLocatedEventBus`.
* The `GpsLab\Domain\Event\Bus\EventBusInterface` renamed to `GpsLab\Domain\Event\Bus\EventBus`.
* The `GpsLab\Domain\Event\Aggregator\AggregateEventsInterface` renamed to `GpsLab\Domain\Event\Aggregator\AggregateEvents`.
* The `GpsLab\Domain\Event\EventInterface` renamed to `GpsLab\Domain\Event\Event`.
* The `GpsLab\Domain\Event\Listener\Locator\LocatorInterface` renamed to `GpsLab\Domain\Event\Listener\Locator\Locator`.
* The `GpsLab\Domain\Event\Queue\EventQueueInterface` renamed to `GpsLab\Domain\Event\Queue\EventQueue`.

## Removed classes and interfaces

* The `GpsLab\Domain\Event\Bus\Bus` has been removed. Use `GpsLab\Domain\Event\Bus\HandlerLocatedEventBus` instead.
* The `GpsLab\Domain\Event\Bus\BusInterface` has been removed. Use `GpsLab\Domain\Event\Bus\EventBus` instead.
* The `GpsLab\Domain\Event\NamedEventInterface` has been removed.
* The `GpsLab\Domain\Event\NameResolver\NamedEventResolver` has been removed.

## Removed methods

* The `GpsLab\Domain\Event\Bus\EventBus::getRegisteredEventListeners()` has been removed.
* The `GpsLab\Domain\Event\Bus\HandlerLocatedEventBus::getRegisteredEventListeners()` has been removed.
* The `GpsLab\Domain\Event\Bus\QueueEventBus::getRegisteredEventListeners()` has been removed.

## Renamed constants

* The `GpsLab\Domain\Event\Queue\PredisEventQueue::FORMAT` renamed to `GpsLab\Domain\Event\Queue\PredisEventQueue::DEFAULT_FORMAT`.
* The `GpsLab\Domain\Event\Queue\PredisUniqueEventQueue::FORMAT` renamed to `GpsLab\Domain\Event\Queue\PredisUniqueEventQueue::DEFAULT_FORMAT`.

## Removed constants

* The `GpsLab\Domain\Event\Queue\PredisEventQueue::LIST_KEY` has been removed.
* The `GpsLab\Domain\Event\Queue\PredisUniqueEventQueue::SET_KEY` has been removed.
