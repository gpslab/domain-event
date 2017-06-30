Executing queue
===============

Queues are designed to distribute the load and delay publishing of events or transfer their publishing to separate
processes.

The handler signed to the event from the executing queue immediately receives the event as it published the queue.
Events are not stored in the queue.

You can use any implementations of [callable type](http://php.net/manual/en/language.types.callable.php) as a queue
subscriber.

```php
use GpsLab\Domain\Event\Bus\HandlerLocatedEventBus;
use GpsLab\Domain\Event\Listener\Locator\DirectBindingEventListenerLocator;
use GpsLab\Domain\Event\Queue\PubSub\ExecutingEventQueue;

$locator = new DirectBindingEventListenerLocator();
$bus = new HandlerLocatedEventBus($locator);
$queue = new ExecutingEventQueue();
```

Subscribe to the queue:

```php
$queue->subscribe(function(ArticleRenamedEvent $event) use ($bus) {
    $bus->publish($event);
});
```

Make event and publish it into queue:

```php
$event = new ArticleRenamedEvent();
$event->new_name = $new_name;

$queue->publish($event);
```

You can use [QueueEventBus](../bus.md) for publish events in queue.
