[![Latest Stable Version](https://img.shields.io/packagist/v/gpslab/domain-event.svg?maxAge=3600&label=stable)](https://packagist.org/packages/gpslab/domain-event)
[![Total Downloads](https://img.shields.io/packagist/dt/gpslab/domain-event.svg?maxAge=3600)](https://packagist.org/packages/gpslab/domain-event)
[![Build Status](https://img.shields.io/travis/gpslab/domain-event.svg?maxAge=3600)](https://travis-ci.org/gpslab/domain-event)
[![Coverage Status](https://img.shields.io/coveralls/gpslab/domain-event.svg?maxAge=3600)](https://coveralls.io/github/gpslab/domain-event?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/gpslab/domain-event.svg?maxAge=3600)](https://scrutinizer-ci.com/g/gpslab/domain-event/?branch=master)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/9c7460e6-51b0-4cc3-9e4c-47066634017b.svg?maxAge=3600&label=SLInsight)](https://insight.sensiolabs.com/projects/9c7460e6-51b0-4cc3-9e4c-47066634017b)
[![StyleCI](https://styleci.io/repos/69552555/shield?branch=master)](https://styleci.io/repos/69552555)
[![License](https://img.shields.io/packagist/l/gpslab/domain-event.svg?maxAge=3600)](https://github.com/gpslab/domain-event)

Domain event
============

Library to create the domain layer of your **DDD** application

## Installation

Pretty simple with [Composer](http://packagist.org), run:

```sh
composer require gpslab/domain-event
```

## Base usage

Create a domain event

```php
use GpsLab\Domain\Event\Event;

final class PurchaseOrderCreatedEvent implements Event
{
    private $customer_id;

    private $create_at;

    public function __construct(CustomerId $customer_id, \DateTimeImmutable $create_at)
    {
        $this->customer_id = $customer_id;
        $this->create_at = $create_at;
    }

    public function getCustomerId()
    {
        return $this->customer_id;
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

final class PurchaseOrder extends AbstractAggregateEventsRaiseInSelf
{
    private $customer_id;

    private $create_at;

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
        $this->customer_id = $event->getCustomerId();
        $this->create_at = $event->getCreateAt();
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

    public function handle(PurchaseOrderCreatedEvent $event)
    {
        $this->mailer->send('recipient@example.com', sprintf(
            'Purchase order created at %s for customer #%s',
            $event->getCreateAt()->format('Y-m-d'),
            $event->getCustomerId()
        ));
    }
}
```

Dispatch events

```php
use GpsLab\Domain\Event\Listener\Locator\NamedEventLocator;
use GpsLab\Domain\Event\NameResolver\EventClassLastPartResolver;
use GpsLab\Domain\Event\Bus\Bus;

// first the locator
$locator = new DirectBindingEventListenerLocator();
// you can use several listeners for one event and one listener for several events
$locator->register(PurchaseOrderCreatedEvent::class, new SendEmailOnPurchaseOrderCreated(/* $mailer */));

// then the event bus
$bus = new HandlerLocatedEventBus($locator);

// do what you need to do on your Domain
$purchase_order = new PurchaseOrder(new CustomerId(1));

// this will clear the list of event in your AggregateEvents so an Event is trigger only once
$bus->pullAndPublish($purchase_order);
```

## Documentation

* [Base usage](docs/base.md)
* [Raise events in self](docs/raise_in_self.md)
* Listener
  * [Create listener](docs/listener.md)
  * Locator
    * [Direct binding locator](docs/listener/locator/direct_binding.md)
    * [Symfony container locator](docs/listener/locator/symfony_container.md)
* Queue
  * [Queue event bus](docs/queue/bus.md)
  * [Queues](docs/queue/queues.md)
* Frameworks
  * [Symfony bundle](https://github.com/gpslab/domain-event-bundle)
* [Middleware](https://github.com/gpslab/middleware)
* [Payload](https://github.com/gpslab/payload)

## License

This bundle is under the [MIT license](http://opensource.org/licenses/MIT). See the complete license in the file: LICENSE
