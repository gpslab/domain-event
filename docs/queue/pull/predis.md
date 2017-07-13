Predis queue
============

Queues are designed to distribute the load and delay publishing of events or transfer their publishing to separate
processes.

The queue stores events in [Redis](https://redis.io/), using the [Predis](https://github.com/nrk/predis) library to
access it.

Predis must be installed separately with [Composer](http://packagist.org):

```
composer require predis/predis
```

This queue uses a [serializer](https://symfony.com/doc/current/components/serializer.html) to convert event objects
to strings and back while waiting for the transport of objects across the Redis. The serializer uses the `predis`
format as a default. You can change format if you need. You can make messages more optimal for a Redis than JSON.

If the message could not be deserialized, then a critical message is written to the log so that the administrator can
react quickly to the problem and the message is placed again at the end of the queue, so as not to lose it.

Configure queue:

```php
use GpsLab\Domain\Event\Queue\Pull\PredisPullEventQueue;
use GpsLab\Domain\Event\Queue\Serializer\SymfonySerializer;
use Symfony\Component\Serializer\Serializer;
use Predis\Client;

//$predis = new Client('tcp://10.0.0.1:6379'); // Predis client
//$symfony_serializer = new Serializer(); // Symfony serializer
//$logger = new Logger(); // PSR-3 logger
$queue_name = 'article_queue';
$format = 'json'; // default: predis
// you can create another implementation of serializer
$serializer = new SymfonySerializer($symfony_serializer, $format);
$queue = new PredisPullEventQueue($predis, $serializer, $logger, $queue_name);
```

Make event and publish it into queue:

```php
$event = new ArticleRenamedEvent();
$event->new_name = $new_name;

$queue->publish($event);
```

In latter pull events from queue:

```php
use GpsLab\Domain\Event\Bus\ListenerLocatedEventBus;
use GpsLab\Domain\Event\Listener\Locator\DirectBindingEventListenerLocator;

$locator = new DirectBindingEventListenerLocator();
$bus = new ListenerLocatedEventBus($locator);

while ($event = $queue->pull()) {
    $bus->publish($event);
}
```

You can use [QueueEventBus](../bus.md) for publish events in queue.
