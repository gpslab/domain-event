AMQP queue
==========

Queues are designed to distribute the load and delay publishing of events or transfer their publishing to separate
processes.

The queue stores events in [RabbitMQ](https://www.rabbitmq.com/), using the [php-amqplib](https://github.com/php-amqplib/php-amqplib)
library to access it.

This queue uses a [serializer](https://symfony.com/doc/current/components/serializer.html) to convert event objects
to strings and back while waiting for the transport of objects across the AMQP. The serializer uses the `predis`
format as a default. You can change format if you need. You can make messages more optimal for a RabbitMQ than JSON.

If the message could not be deserialized, then a critical message is written to the log so that the administrator can
react quickly to the problem and the message is placed again at the end of the queue, so as not to lose it.

You can use any implementations of [callable type](http://php.net/manual/en/language.types.callable.php) as a queue
subscriber.

Configure queue:

```php
use GpsLab\Domain\Event\Queue\Subscribe\AMQPSubscribeEventQueue;
use GpsLab\Domain\Event\Queue\Serializer\SymfonySerializer;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Symfony\Component\Serializer\Serializer;

//$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest'); // AMQP connection
//$channel = $connection->channel();
//$symfony_serializer = new Serializer(); // Symfony serializer
//$logger = new Logger(); // PSR-3 logger
$queue_name = 'article_queue';
$format = 'json'; // default: predis
// you can create another implementation of serializer
$serializer = new SymfonySerializer($symfony_serializer, $format);
$queue = new AMQPSubscribeEventQueue($channel, $serializer, $logger, $queue_name);
```

Subscribe to the queue:

```php
use GpsLab\Domain\Event\Bus\ListenerLocatedEventBus;
use GpsLab\Domain\Event\Listener\Locator\DirectBindingEventListenerLocator;

$locator = new DirectBindingEventListenerLocator();
$bus = new ListenerLocatedEventBus($locator);

$handler = function(ArticleRenamedEvent $event) use ($bus) {
    $bus->publish($event);
};

$queue->subscribe($handler);
```

You can unsubscribe of the queue:

```php
$queue->unsubscribe($handler);
```

Make event and publish it into queue:

```php
$event = new ArticleRenamedEvent();
$event->new_name = $new_name;

$queue->publish($event);
```

You can use [QueueEventBus](../bus.md) for publish events in queue.
