Memory queue
============

Queues are designed to distribute the load and delay publishing of events or transfer their publishing to separate
processes.

Memory queue stores events in an internal variable, which allows you to delay execution of events at the end of the
script execution.

```php
use GpsLab\Domain\Event\Bus\HandlerLocatedEventBus;
use GpsLab\Domain\Event\Listener\Locator\DirectBindingEventListenerLocator;
use GpsLab\Domain\Event\Queue\Pull\MemoryPullEventQueue;

$locator = new DirectBindingEventListenerLocator();
$bus = new HandlerLocatedEventBus($locator);
$queue = new MemoryPullEventQueue();

$event = new ArticleRenamedEvent();
$event->new_name = $new_name;

$queue->publish($event);
```

In latter

```php
while ($event = $queue->pull()) {
    $bus->publish($event);
}
```

You can use [QueueEventBus](../bus.md) for publish events in queue.
