UPGRADE FROM 1.x to 2.0
=======================

## Renamed

* The `GpsLab\Domain\Event\Bus\EventBus` renamed to `GpsLab\Domain\Event\Bus\HandlerLocatedEventBus`.
* The `GpsLab\Domain\Event\Bus\EventBusInterface` has been removed. Use `GpsLab\Domain\Event\Bus\EventBus` instead.

## Removed

* The `GpsLab\Domain\Event\Bus\Bus` has been removed. Use `GpsLab\Domain\Event\Bus\HandlerLocatedEventBus` instead.
* The `GpsLab\Domain\Event\Bus\BusInterface` has been removed. Use `GpsLab\Domain\Event\Bus\EventBus` instead.
* The `GpsLab\Domain\Event\Bus\EventBus::getRegisteredEventListeners()` has been removed.
* The `GpsLab\Domain\Event\Bus\HandlerLocatedEventBus::getRegisteredEventListeners()` has been removed.
* The `GpsLab\Domain\Event\Bus\QueueEventBus::getRegisteredEventListeners()` has been removed.
