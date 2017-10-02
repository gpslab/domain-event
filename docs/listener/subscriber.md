Event subscriber
================

You can handle many events in one subscriber.

```php
class PurchaseOrderSubscriber implements Subscriber
{
    public static function subscribedEvents()
    {
        return [
            PurchaseOrderCreatedEvent::class => ['handlePurchaseOrderCreated'],
            PurchaseOrderCompletedEvent::class => ['handlePurchaseOrderCompleted'],
        ];
    }

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
$locator->registerSubscriber(new PurchaseOrderSubscriber());
```

## Listener locator

You can use exists locators of listener:

 * [Direct binding locator](locator/direct_binding.md)
 * [PSR-11 container aware locator](locator/psr-11_container.md)
 * [Symfony container aware locator](locator/symfony_container.md)

Or you can create custom locator that implements `GpsLab\Domain\Event\Listener\Locator\EventListenerLocator` interface.
