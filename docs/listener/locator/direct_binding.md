Direct binding event listener locator
=====================================

Create a domain event

```php
use GpsLab\Domain\Event\Event;

class PurchaseOrderCreatedEvent implements Event
{
    private $customer_id;
    private $create_at;

    public function __construct(CustomerId $customer_id, \DateTimeImmutable $create_at)
    {
        $this->customer_id = $customer_id;
        $this->create_at = $create_at;
    }

    public function customerId()
    {
        return $this->customer_id;
    }

    public function createAt()
    {
        return $this->create_at;
    }
}
```

Raise your event

```php
use GpsLab\Domain\Event\Aggregator\AbstractAggregateEvents;

final class PurchaseOrder extends AbstractAggregateEvents
{
    public function __construct(CustomerId $customer_id)
    {
        $this->raise(new PurchaseOrderCreatedEvent($customer_id, new \DateTimeImmutable()));
    }
}
```

Create listener

```php
class SendEmailOnPurchaseOrderCreated
{
    private $mailer;

    public function __construct($mailer)
    {
        $this->mailer = $mailer;
    }

    public function handlePurchaseOrderCreated(PurchaseOrderCreatedEvent $event)
    {
        $this->mailer->send('recipient@example.com', sprintf(
            'Purchase order created at %s for customer #%s',
            $event->createAt()->format('Y-m-d'),
            $event->customerId()
        ));
    }
}
```

Create event listener bus and publish events in it

```php
use GpsLab\Domain\Event\Listener\Locator\DirectBindingEventListenerLocator;
use GpsLab\Domain\Event\Bus\Bus;

// first the locator
$locator = new DirectBindingEventListenerLocator();
// you can use several listeners for one event and one listener for several events
$locator->register(
    PurchaseOrderCreatedEvent::class,
    [new SendEmailOnPurchaseOrderCreated(/* $mailer */), 'handlePurchaseOrderCreated']
);

// then the event bus
$bus = new ListenerLocatedEventBus($locator);

// do what you need to do on your Domain
$purchase_order = new PurchaseOrder(new CustomerId(1));

// this will clear the list of event in your AggregateEvents so an Event is trigger only once
$events = $purchase_order->pullEvents();

// You can have more than one event at a time.
foreach($events as $event) {
    $bus->publish($event);
}

// You can use one method
//$bus->pullAndPublish($purchase_order);
```
