Base usage
==========

Create a domain event

```php
use GpsLab\Domain\Event\EventInterface;

class PurchaseOrderCreatedEvent implements EventInterface
{
    public function __construct(Customer $customer, \DateTimeImmutable $create_at)
    {
        // store data
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
        $this->raise(new PurchaseOrderCreatedEvent($customer, new \DateTimeImmutable()));
    }
}
```

Dispatch events

```php
// do what you need to do on your Domain
$purchase_order = new PurchaseOrder(new Customer(1));

// this will clear the list of event in your AggregateEvents so an Event is trigger only once
$events = $purchase_order->pullEvents();
```

## Traits

You can use [Traits](http://php.net/manual/en/language.oop5.traits.php) for raise your event

```php
use GpsLab\Domain\Event\Aggregator\AggregateEventsTrait;
use GpsLab\Domain\Event\Aggregator\AggregateEventsInterface;

final class PurchaseOrder implements AggregateEventsInterface
{
    use AggregateEventsTrait;

    public function __construct(Customer $customer)
    {
        $this->raise(new PurchaseOrderCreatedEvent($customer, new \DateTimeImmutable()));
    }
}
```
