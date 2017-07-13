Base usage
==========

Create a domain event

```php
use GpsLab\Domain\Event\Event

class PurchaseOrderCreatedEvent implements Event
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
    private $customer_id;

    private $create_at;

    public function __construct(CustomerId $customer_id)
    {
        $this->customer_id = $customer_id;
        $this->create_at = new \DateTimeImmutable();

        $this->raise(new PurchaseOrderCreatedEvent($customer_id, $this->create_at));
    }
}
```

Dispatch events

```php
// do what you need to do on your Domain
$purchase_order = new PurchaseOrder(new CustomerId(1));

// this will clear the list of event in your AggregateEvents so an Event is trigger only once
$events = $purchase_order->pullEvents();
```

Traits
------

You can use [Traits](http://php.net/manual/en/language.oop5.traits.php) for raise your event

```php
use GpsLab\Domain\Event\Aggregator\AggregateEventsTrait;
use GpsLab\Domain\Event\Aggregator\AggregateEvents;

final class PurchaseOrder implements AggregateEvents
{
    use AggregateEventsTrait;

    private $customer_id;

    private $create_at;

    public function __construct(CustomerId $customer_id)
    {
        $this->customer_id = $customer_id;
        $this->create_at = new \DateTimeImmutable();

        $this->raise(new PurchaseOrderCreatedEvent($customer_id, $this->create_at));
    }
}
```
