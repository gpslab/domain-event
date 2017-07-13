Event listener
==============

You can use any implementations of [callable type](http://php.net/manual/en/language.types.callable.php) as a event
listener.

The event listener can be a [anonymous function](http://php.net/manual/en/functions.anonymous.php):

```php
$listener = function (PurchaseOrderCreatedEvent $event) {
    // do something
};

// register event listener in listener locator
$locator = new DirectBindingEventListenerLocator();
$locator->register(PurchaseOrderCreatedEvent::class, $listener);
```

It can be a some function:

```php
function PurchaseOrderCreatedListener(PurchaseOrderCreatedEvent $event)
{
    // do something
}

// register event listener in listener locator
$locator = new DirectBindingEventListenerLocator();
$locator->register(PurchaseOrderCreatedEvent::class, 'PurchaseOrderCreatedListener');
```

It can be a [called object](http://php.net/manual/en/language.oop5.magic.php#object.invoke):

```php
class PurchaseOrderCreatedListener
{
    public function __invoke(PurchaseOrderCreatedEvent $event)
    {
        // do something
    }
}

// register event listener in listener locator
$locator = new DirectBindingEventListenerLocator();
$locator->register(PurchaseOrderCreatedEvent::class, new PurchaseOrderCreatedListener());
```

It can be a static method of class:

```php
class PurchaseOrderCreatedListener
{
    public static function handlePurchaseOrderCreated(PurchaseOrderCreatedEvent $event)
    {
        // do something
    }
}

// register event listener in listener locator
$locator = new DirectBindingEventListenerLocator();
$locator->register(PurchaseOrderCreatedEvent::class, 'PurchaseOrderCreatedListener::handlePurchaseOrderCreated');
```

It can be a public method of class:

```php
class PurchaseOrderCreatedListener
{
    public function handlePurchaseOrderCreated(PurchaseOrderCreatedEvent $event)
    {
        // do something
    }
}

// register event listener in listener locator
$locator = new DirectBindingEventListenerLocator();
$locator->register(PurchaseOrderCreatedEvent::class, [new PurchaseOrderCreatedListener(), 'handlePurchaseOrderCreated']);
```

You can handle many events in one listener.

```php
class PurchaseOrderListener
{
    public function handlePurchaseOrderCreated(PurchaseOrderCreatedEvent $event)
    {
        // do something
    }

    public function handlePurchaseOrderCompleted(PurchaseOrderCompletedEvent $event)
    {
        // do something
    }
}

$listener = new PurchaseOrderListener();

// register event listener in listener locator
$locator = new DirectBindingEventListenerLocator();
$locator->register(PurchaseOrderCreatedEvent::class, [$listener, 'handlePurchaseOrderCreated']);
$locator->register(PurchaseOrderCompletedEvent::class, [$listener, 'handlePurchaseOrderCompleted']);
```

## Listener locator

You can use exists locators of listener:

 * [Direct binding locator](listener/locator/direct_binding.md)
 * [PSR-11 container aware locator](listener/locator/psr-11_container.md)
 * [Symfony container aware locator](listener/locator/symfony_container.md)

Or you can create custom locator that implements `GpsLab\Domain\Event\Listener\Locator\EventListenerLocator` interface.
