[![Latest Stable Version](https://img.shields.io/packagist/v/gpslab/domain-event.svg?maxAge=3600&label=stable)](https://packagist.org/packages/gpslab/domain-event)
[![Latest Unstable Version](https://img.shields.io/packagist/vpre/gpslab/domain-event.svg?maxAge=3600&label=unstable)](https://packagist.org/packages/gpslab/domain-event)
[![Total Downloads](https://img.shields.io/packagist/dt/gpslab/domain-event.svg?maxAge=3600)](https://packagist.org/packages/gpslab/domain-event)
[![Build Status](https://img.shields.io/travis/gpslab/domain-event.svg?maxAge=3600)](https://travis-ci.org/gpslab/domain-event)
[![Coverage Status](https://img.shields.io/coveralls/gpslab/domain-event.svg?maxAge=3600)](https://coveralls.io/github/gpslab/domain-event?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/gpslab/domain-event.svg?maxAge=3600)](https://scrutinizer-ci.com/g/gpslab/domain-event/?branch=master)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/9c7460e6-51b0-4cc3-9e4c-47066634017b.svg?maxAge=3600&label=SLInsight)](https://insight.sensiolabs.com/projects/9c7460e6-51b0-4cc3-9e4c-47066634017b)
[![StyleCI](https://styleci.io/repos/69552555/shield?branch=master)](https://styleci.io/repos/69552555)
[![License](https://img.shields.io/github/license/gpslab/domain-event.svg?maxAge=3600)](https://github.com/gpslab/domain-event)

Domain event
============

Library to create the domain layer of your DDD application

## Installation

Pretty simple with [Composer](http://packagist.org), run:

```sh
composer require gpslab/domain-event
```
## Usage

Create a domain event

```
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

You can use [Traits](http://php.net/manual/en/language.oop5.traits.php) for raise your event

```php
use GpsLab\Domain\Event\Aggregator\AggregateEventsTrait;
use GpsLab\Domain\Event\Aggregator\AggregateEventsInterface;

final class PurchaseOrder implements AggregateEventsInterface
{
    use AggregateEventsTrait;

    public function __construct(Customer $customer)
    {
        $this->raise(new PurchaseOrderCreated($customer, new \DateTimeImmutable()));
    }
}
```

Dispatch events

```
// do what you need to do on your Domain
$purchase_order = new PurchaseOrder(new Customer(1));

// this will clear the list of event in your AggregateEvents so an Event is trigger only once
$events = $purchase_order->pullEvents();
```

## Use EventClassLocator

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
use GpsLab\Domain\Event\Listener\Locator\EventClassLocator;
use GpsLab\Domain\Event\Bus\Bus;

// first the locator
$locator = new EventClassLocator();
// you can use one listener for several events
$locator->register(PurchaseOrderCreated::class, new SendEmailOnPurchaseOrderCreated(/* $mailer */));

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

## Use VoterLocator

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

## License

This bundle is under the [MIT license](http://opensource.org/licenses/MIT). See the complete license in the file: LICENSE
