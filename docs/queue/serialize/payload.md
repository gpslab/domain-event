Payload Symfony serializer
==========================

If you use the [Payload package](https://github.com/gpslab/payload), you can simplify the serialization of your
specific events.

```php
use GpsLab\Domain\Event\Event;
use GpsLab\Component\Payload\Payload;
use Symfony\Component\Serializer\Exception\UnsupportedException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ArticleRenamedEventSerializer implements NormalizerInterface, DenormalizerInterface
{
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ArticleRenamedEvent;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        return [
            'type' => 'ArticleRenamed',
            'payload' => $object->payload(),
        ];
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if ($data['type'] !== 'ArticleRenamed') {
            throw new UnsupportedException();
        }

        return new ArticleRenamedEvent($data['payload']);
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === Event::class && isset($data['type'], $data['payload']) && $data['type'] === 'ArticleRenamed';
    }
}
```

You can use [universal serializers](https://github.com/gpslab/payload#serialize) from Payload package and wrap it.
Remember that the `$type` and `$class` for denormalization will always be equal to `GpsLab\Domain\Event\Event`
in `PredisPullEventQueue` and `PredisSubscribeEventQueue`.
