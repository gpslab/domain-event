Use VoterLocator
================

Create a domain event

```php
use GpsLab\Domain\Event\EventInterface;

class PurchaseOrderCreated implements EventInterface
{
    private $customer;
    private $create_at;

    public function __construct(Customer $customer, \DateTimeImmutable $create_at)
    {
        $this->customer = $customer;
        $this->create_at = $create_at;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function getCreateAt()
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
    public function __construct(Customer $customer)
    {
        $this->raise(new PurchaseOrderCreated($customer, new \DateTimeImmutable()));
    }
}
```

Create listener

```php
use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\Listener\VoterListenerInterface;

class SendEmailOnPurchaseOrderCreated implements VoterListenerInterface
{
    private $mailer;

    public function __construct($mailer)
    {
        $this->mailer = $mailer;
    }

    public function isSupportedEvent(EventInterface $event);
    {
        // you can add more conditions
        return $event instanceof PurchaseOrderCreated;
    }

    public function handle(EventInterface $event)
    {
        $this->mailer->send('to@you.com', sprintf(
            'Purchase order created at %s for customer #%s',
            $event->getCreateAt()->format('Y-m-d'),
            $event->getCustomer()->getId()
        ));
    }
}
```

Create event listener bus and publish events in it

```php
use GpsLab\Domain\Event\Listener\Locator\VoterLocator;
use GpsLab\Domain\Event\Bus\Bus;

// first the locator
$locator = new VoterLocator();
// you can use several listeners for one event and one listener for several events
$locator->register(new SendEmailOnPurchaseOrderCreated(/* $mailer */));

// then the event bus
$bus = new Bus($locator);

// do what you need to do on your Domain
$purchase_order = new PurchaseOrder(new Customer(1));

// this will clear the list of event in your AggregateEvents so an Event is trigger only once
$events = $purchase_order->pullEvents();

// You can have more than one event at a time.
foreach($events as $event) {
    $bus->publish($event);
}

// You can use one method
//$bus->pullAndPublish($purchase_order);
```
