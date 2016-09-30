Raise events in self
====================

Create a domain event

```php
use GpsLab\Domain\Event\EventInterface;

class PurchaseOrderCreated implements EventInterface
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

final class PurchaseOrder extends AbstractAggregateEventsRaiseInSelf
{
    /**
     * @var mixed
     */
    private $customer_id;

    public function __construct(Customer $customer)
    {
        $this->raise(new PurchaseOrderCreated($customer, new \DateTimeImmutable()));
    }

    /**
     * The raise() method will automatically call this method.
     * Since it's an event you should never do some tests in this method.
     * Try to think that an Event is something that happened in the past.
     * You can not modify what happened. The only thing that you can do is create another event to compensate.
     * You do not obliged to listen this event and are not required to create this method.
     */
    public function onPurchaseOrderCreated(PurchaseOrderCreated $event)
    {
        $this->customer_id = $event->getCustomer()->getId();
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
    use AggregateEventsRaiseInSelfTrait;

    /**
     * @var mixed
     */
    private $customer_id;

    public function __construct(Customer $customer)
    {
        $this->raise(new PurchaseOrderCreated($customer, new \DateTimeImmutable()));
    }

    public function onPurchaseOrderCreated(PurchaseOrderCreated $event)
    {
        $this->customer_id = $event->getCustomer()->getId();
    }
}
```

## Resolve event name

As a default used `EventClassLastPartResolver` event name resolver.
Conversion examples:

* `\PurchaseOrderCreated` > `PurchaseOrderCreated`
* `\PurchaseOrderCreatedEvent` > `PurchaseOrderCreated`
* `\Acme\Demo\Domain\PurchaseOrder\Event\PurchaseOrderCreated` > `PurchaseOrderCreated`
* `\Acme\Demo\Domain\PurchaseOrder\Event\PurchaseOrderCreatedEvent` > `PurchaseOrderCreated`
* `\Acme_Demo_Domain_PurchaseOrder_Event_PurchaseOrderCreated` > `PurchaseOrderCreated`
* `\Acme_Demo_Domain_PurchaseOrder_Event_PurchaseOrderCreatedEvent` > `PurchaseOrderCreated`

You can change default event name resolver. Create a named domain event first

```php
use GpsLab\Domain\Event\EventInterface;

class PurchaseOrderCreated implements NamedEventInterface
{
    private $customer;

    public function __construct(Customer $customer, \DateTimeImmutable $create_at)
    {
        $this->customer = $customer;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Used this name for resolver the event name
     */
    public function getName()
    {
        return 'Created';
    }
}
```

Change default event name resolver

```php
use GpsLab\Domain\Event\Aggregator\AbstractAggregateEvents;

final class PurchaseOrder extends AbstractAggregateEventsRaiseInSelf
{
    /**
     * @var mixed
     */
    private $customer_id;

    public function __construct(Customer $customer)
    {
        $this->changeEventNameResolver(new NamedEventResolver());
        $this->raise(new PurchaseOrderCreated($customer, new \DateTimeImmutable()));
    }

    /**
     * Method name used from NamedEventInterface::getName()
     */
    public function onCreated(PurchaseOrderCreated $event)
    {
        $this->customer_id = $event->getCustomer()->getId();
    }
}
```

You can create a custom event name resolver. For this you need to implement `EventNameResolverInterface` interface.
