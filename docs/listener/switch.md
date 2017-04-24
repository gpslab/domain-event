Switch event listener
=====================

You can create listener for handle several events.

For example, let's create a listener that sends emails when handle events:

```php
use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\Listener\ListenerInterface;

class SendEmailOnPurchaseOrderEvents implements ListenerInterface
{
    private $mailer;

    public function __construct($mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(EventInterface $event)
    {
        if ($event instanceof PurchaseOrderCreatedEvent) {
            $this->mailer->send('recipient@example.com', sprintf(
                'Purchase order created at %s for customer #%s',
                $event->getCreateAt()->format('Y-m-d'),
                $event->getCustomer()->getId()
            ));
        }

        if ($event instanceof PurchaseOrderApprovedEvent) {
            $this->mailer->send('recipient@example.com', sprintf(
                'Purchase order approved at %s for customer #%s',
                $event->getCreateAt()->format('Y-m-d'),
                $event->getCustomer()->getId()
            ));
        }
    }
}
```

Another handler that logs the handled events:

```php
use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\Listener\ListenerInterface;
use Psr\Log\LoggerInterface;

class LogPurchaseOrderEvents implements ListenerInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(EventInterface $event)
    {
        if ($event instanceof PurchaseOrderCreatedEvent) {
            $this->logger->info(sprintf(
                'Purchase order created at %s for customer #%s',
                $event->getCreateAt()->format('Y-m-d'),
                $event->getCustomer()->getId()
            ));
        }

        if ($event instanceof PurchaseOrderApprovedEvent) {
            $this->logger->info(sprintf(
                'Purchase order approved at %s for customer #%s',
                $event->getCreateAt()->format('Y-m-d'),
                $event->getCustomer()->getId()
            ));
        }
    }
}
```

Register listeners

```php
$locator->register('PurchaseOrderCreated', new SendEmailOnPurchaseOrderEvents(/* $mailer */));
$locator->register('PurchaseOrderApproved', new SendEmailOnPurchaseOrderEvents(/* $mailer */));

$locator->register('PurchaseOrderCreated', new LogPurchaseOrderEvents(/* $logger */));
$locator->register('PurchaseOrderApproved', new LogPurchaseOrderEvents(/* $logger */));
```

To get rid of unnecessary conditions in the handler, you can use the switch:

```php
use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\Listener\AbstractSwitchListener;

class SendEmailOnPurchaseOrderEvents extends AbstractSwitchListener
{
    private $mailer;

    public function __construct($mailer)
    {
        $this->mailer = $mailer;
    }

    protected function handlePurchaseOrderCreated(PurchaseOrderCreatedEvent $event)
    {
        $this->mailer->send('recipient@example.com', sprintf(
            'Purchase order created at %s for customer #%s',
            $event->getCreateAt()->format('Y-m-d'),
            $event->getCustomer()->getId()
        ));
    }

    protected function handlePurchaseOrderApproved(PurchaseOrderApprovedEvent $event)
    {
        $this->mailer->send('recipient@example.com', sprintf(
            'Purchase order approved at %s for customer #%s',
            $event->getCreateAt()->format('Y-m-d'),
            $event->getCustomer()->getId()
        ));
    }
}
```

And listener for logging:

```php
use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\Listener\AbstractSwitchListener;
use Psr\Log\LoggerInterface;

class LogPurchaseOrderEvents extends AbstractSwitchListener
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function handlePurchaseOrderCreated(PurchaseOrderCreatedEvent $event)
    {
        $this->logger->info(sprintf(
            'Purchase order created at %s for customer #%s',
            $event->getCreateAt()->format('Y-m-d'),
            $event->getCustomer()->getId()
        ));
    }

    protected function handlePurchaseOrderApproved(PurchaseOrderApprovedEvent $event)
    {
        $this->logger->info(sprintf(
            'Purchase order approved at %s for customer #%s',
            $event->getCreateAt()->format('Y-m-d'),
            $event->getCustomer()->getId()
        ));
    }
}
```

This approach is simpler and more informative. Also, we can use `protected` access for event handlers in this case.

## Traits

You can use [Traits](http://php.net/manual/en/language.oop5.traits.php) for switch your event handle:

```php
use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\Listener\ListenerInterface;
use GpsLab\Domain\Event\Listener\SwitchListenerTrait;

class SendEmailOnPurchaseOrderEvents implements ListenerInterface
{
    use SwitchListenerTrait;

    private $mailer;

    public function __construct($mailer)
    {
        $this->mailer = $mailer;
    }

    protected function handlePurchaseOrderCreated(PurchaseOrderCreatedEvent $event)
    {
        $this->mailer->send('recipient@example.com', sprintf(
            'Purchase order created at %s for customer #%s',
            $event->getCreateAt()->format('Y-m-d'),
            $event->getCustomer()->getId()
        ));
    }

    protected function handlePurchaseOrderApproved(PurchaseOrderApprovedEvent $event)
    {
        $this->mailer->send('recipient@example.com', sprintf(
            'Purchase order approved at %s for customer #%s',
            $event->getCreateAt()->format('Y-m-d'),
            $event->getCustomer()->getId()
        ));
    }
}
```
