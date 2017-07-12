Queue event bus
===============

You can publish events from base events bus and you can push events in queue and publish it in later.

```php
use GpsLab\Domain\Event\Bus\QueueEventBus;
use GpsLab\Domain\Event\Bus\ListenerLocatedEventBus;
use GpsLab\Domain\Event\Listener\Locator\DirectBindingEventListenerLocator;
use GpsLab\Domain\Event\Queue\Pull\MemoryPullEventQueue;

// event listener locator
$locator = new DirectBindingEventListenerLocator();

// base events bus
$publisher_bus = new ListenerLocatedEventBus($locator);

// queue storing events in memory
$queue = MemoryPullEventQueue();

$bus = new QueueBus($queue);
```

Do what you need to do on your Domain

```php
$purchase_order = new PurchaseOrder(new Customer(1));

// push events to queue
$bus->pullAndPublish($purchase_order);
```

In later you can pull events from queue

```php
// publish events from queue
while ($event = $queue->pull()) {
    $publisher_bus->publish($event);
}
```

You can use [Subscribe queue](subscribe/subscribe.md) for optimization.
