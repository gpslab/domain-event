Symfony container aware event listener locator
==============================================

`SymfonyContainerAwareLocator` uses the `NamedEventLocator` as a base class. See how to use it:

 * [Event class locator](event_class.md)
 * [Event class last part locator](event_class_last_part.md)
 * [Named event locator](named_event.md)

## Require

Require Symfony [DependencyInjection](https://symfony.com/doc/current/components/dependency_injection.html) component.

## Use last part of event class as event name

Create a domain event

```php
use GpsLab\Domain\Event\EventInterface;

class PurchaseOrderCreatedEvent implements EventInterface
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
        $this->raise(new PurchaseOrderCreatedEvent($customer, new \DateTimeImmutable()));
    }
}
```

Create listener

```php
use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\Listener\ListenerInterface;

class SendEmailOnPurchaseOrderCreated implements ListenerInterface
{
    private $mailer;

    public function __construct($mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(EventInterface $event)
    {
        $this->mailer->send('recipient@example.com', sprintf(
            'Purchase order created at %s for customer #%s',
            $event->getCreateAt()->format('Y-m-d'),
            $event->getCustomer()->getId()
        ));
    }
}
```

Create event listener bus and publish events in it

```php
use Symfony\Component\DependencyInjection\Container;
use GpsLab\Domain\Event\Listener\Locator\ContainerAwareLocator;
use GpsLab\Domain\Event\NameResolver\EventClassLastPartResolver;
use GpsLab\Domain\Event\Bus\Bus;

// use last part of event class as event name
$resolver = new EventClassLastPartResolver();

// registr listener service in container
$container = new Container();
$container->set('purchase_order.created.send_email', new SendEmailOnPurchaseOrderCreated(/* $mailer */));

// first the locator
$locator = new SymfonyContainerAwareLocator($resolver);
$locator->setContainer($container);
// you can use several listeners for one event and one listener for several events
$locator->registerService(PurchaseOrderCreatedEvent::NAME, 'purchase_order.created.send_email');

// then the event bus
$bus = new HandlerLocatedEventBus($locator);

// do what you need to do on your Domain
$purchase_order = new PurchaseOrder(new Customer(1));

// this will clear the list of event in your AggregateEvents so an Event is trigger only once
$events = $purchase_order->pullEvents();

// you can have more than one event at a time.
foreach($events as $event) {
    $bus->publish($event);
}

// You can use one method
//$bus->pullAndPublish($purchase_order);
```
