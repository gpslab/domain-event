Queue event bus
===============

You can publish events from base events bus and you can push events in queue and publish it in later.

```php
// base events bus
$publisher_bus = new Bus($locator);

// queue storing events in memory
$queue = MemoryEventQueue();

$bus = new QueueBus($queue, $publisher_bus);


// do what you need to do on your Domain
$purchase_order = new PurchaseOrder(new Customer(1));

// push events to queue
$bus->pullAndPublish($purchase_order);

// in later ...

// publish events from queue
$bus->publishFromQueue();
```
