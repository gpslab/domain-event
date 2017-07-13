Raise events in self
====================

Create a domain event

```php
use GpsLab\Domain\Event\Event;

class PurchaseOrderCreatedEvent implements Event
{
    public function __construct(CustomerId $customer_id, \DateTimeImmutable $create_at)
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

    public function __construct(CustomerId $customer_id)
    {
        $this->raise(new PurchaseOrderCreatedEvent($customer_id, new \DateTimeImmutable()));
    }

    /**
     * The raise() method will automatically call this method.
     * Since it's an event you should never do some tests in this method.
     * Try to think that an Event is something that happened in the past.
     * You can not modify what happened. The only thing that you can do is create another event to compensate.
     * You do not obliged to listen this event and are not required to create this method.
     */
    protected function onPurchaseOrderCreated(PurchaseOrderCreatedEvent $event)
    {
        $this->customer_id = $event->customerId();
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
use GpsLab\Domain\Event\Aggregator\AggregateEventsRaiseInSelfTrait;
use GpsLab\Domain\Event\Aggregator\AggregateEvents;

final class PurchaseOrder implements AggregateEvents
{
    use AggregateEventsRaiseInSelfTrait;

    /**
     * @var mixed
     */
    private $customer_id;

    public function __construct(CustomerId $customer_id)
    {
        $this->raise(new PurchaseOrderCreatedEvent($customer_id, new \DateTimeImmutable()));
    }

    protected function onPurchaseOrderCreated(PurchaseOrderCreatedEvent $event)
    {
        $this->customer_id = $event->customerId();
    }
}
```

Change default event handler method name
----------------------------------------

Create a named domain event first

```php
use GpsLab\Domain\Event\Event;

class PurchaseOrderCreatedEvent implements Event
{
    private $customer_id;

    public function __construct(CustomerId $customer_id, \DateTimeImmutable $create_at)
    {
        $this->customer_id = $customer_id;
    }

    public function customerId()
    {
        return $this->customer_id;
    }

    /**
     * Used this name in handler event name resolver
     */
    public function getName()
    {
        return 'Created';
    }
}
```

Then override the method that generates the name of the handler method in entity:

```php
use GpsLab\Domain\Event\Aggregator\AbstractAggregateEvents;
use GpsLab\Domain\Event\Event;

final class PurchaseOrder extends AbstractAggregateEventsRaiseInSelf
{
    /**
     * @var mixed
     */
    private $customer_id;

    public function __construct(CustomerId $customer_id)
    {
        $this->raise(new PurchaseOrderCreatedEvent($customer_id, new \DateTimeImmutable()));
    }

    protected function getMethodNameFromEvent(Event $event)
    {
        return 'on'.$event->getName();
    }

    protected function onCreated(PurchaseOrderCreatedEvent $event)
    {
        $this->customer_id = $event->customerId();
    }
}
```
