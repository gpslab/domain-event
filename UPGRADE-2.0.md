UPGRADE FROM 1.x to 2.0
=======================

## Renamed

* The `GpsLab\Domain\Event\Bus\EventBus` renamed to `GpsLab\Domain\Event\Bus\HandlerLocatedEventBus`.
* The `GpsLab\Domain\Event\Bus\EventBusInterface` renamed to `GpsLab\Domain\Event\Bus\EventBus`.
* The `GpsLab\Domain\Event\Aggregator\AggregateEventsInterface` renamed to `GpsLab\Domain\Event\Aggregator\AggregateEvents`.
* The `GpsLab\Domain\Event\EventInterface` renamed to `GpsLab\Domain\Event\Event`.
* The `GpsLab\Domain\Event\Listener\Locator\LocatorInterface` renamed to `GpsLab\Domain\Event\Listener\Locator\Locator`.

## Removed

* The `GpsLab\Domain\Event\Bus\Bus` has been removed. Use `GpsLab\Domain\Event\Bus\HandlerLocatedEventBus` instead.
* The `GpsLab\Domain\Event\Bus\BusInterface` has been removed. Use `GpsLab\Domain\Event\Bus\EventBus` instead.
* The `GpsLab\Domain\Event\Bus\EventBus::getRegisteredEventListeners()` has been removed.
* The `GpsLab\Domain\Event\Bus\HandlerLocatedEventBus::getRegisteredEventListeners()` has been removed.
* The `GpsLab\Domain\Event\Bus\QueueEventBus::getRegisteredEventListeners()` has been removed.
* The `GpsLab\Domain\Event\NamedEventInterface` has been removed.
* The `GpsLab\Domain\Event\NameResolver\NamedEventResolver` has been removed.
