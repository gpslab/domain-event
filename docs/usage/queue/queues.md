Queues
======

You can use one of the existing queues:

* `MemoryEventQueue` - storage of events in memory, in the current PHP process;
* `MemoryUniqueEventQueue` - storage of unique events in memory, in the current process;
* `PredisEventQueue` - storage of events in Redis using [predis/predis](https://packagist.org/packages/predis/predis);
* `PredisUniqueEventQueue` - storage of unique events in Redis using
[predis/predis](https://packagist.org/packages/predis/predis).

You can create your own queue service by implementing the interface `EventQueueInterface`.

## Redis event queue

`PredisEventQueue` using [predis/predis](https://packagist.org/packages/predis/predis) for access to Redis and
[Symfony Serializer](http://symfony.com/doc/current/components/serializer.html) for serialize events and store its in
Redis.

> **Important!** If the event failed to deserialize when it was received from the queue, it is placed at the end of the
> queue and an error message is written to the log.

To store all events, used the [List](https://redis.io/topics/data-types-intro#redis-lists) data type.

To store unique events, used the [Set](https://redis.io/topics/data-types-intro#redis-sets) data type.

## Example event serializer

Example serializer of `PurchaseOrderCreatedEvent` event:

```php
use GpsLab\Domain\Event\EventInterface;
use GpsLab\Domain\Event\Queue\PredisEventQueue;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Exception\UnsupportedException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PurchaseOrderCreatedEventSerializer implements NormalizerInterface, DenormalizerInterface
{
    const PATTERN = 'PurchaseOrderCreated;%s;%s';
    const REGEXP = '/^
            PurchaseOrderCreated;                                # type
            (?<customer_id>\d+);                                 # customer id
            (?<create_at>\d{4}\-\d{2}\-\d{2}\s\d{2}:\d{2}:\d{2}) # date create
        $/x';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof PurchaseOrderCreatedEvent && $format == PredisEventQueue::FORMAT;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        return sprintf(
            self::PATTERN,
            $object->getCustomer()->getId(),
            $object->getCreateAt()->format('Y-m-d H:i:s')
        );
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (!preg_match(self::REGEXP, $data, $match)) {
            throw new UnsupportedException();
        }

        // get customer by id
        $customer = $this->em->getRepository(Customer::class)->find($match['customer_id']);

        // customer not found
        if (!($customer instanceof Customer)) {
            throw new UnexpectedValueException();
        }

        return new PurchaseOrderCreated($customer, new \DateTime($match['create_at']));
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return (
            $format === PredisEventQueue::FORMAT &&
            $type === EventInterface::class &&
            preg_match(self::REGEXP, $data)
        );
    }
}
```
