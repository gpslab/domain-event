Simple payload serializer
=========================

If you use the [Payload package](https://github.com/gpslab/payload), you can simplify the serialization of your
specific events.

```php
use GpsLab\Domain\Event\Event;
use GpsLab\Domain\Event\Queue\Serializer\Serializer;

class JsonPayloadSerializer implements Serializer
{
    /**
     * @param object $data
     *
     * @return string
     */
    public function serialize($data)
    {
        return json_encode([
            'class' => get_class($data),
            'payload' => $object->payload(),
        ]);
    }

    /**
     * @param string $data
     *
     * @return object
     */
    public function deserialize($data)
    {
        $data = json_decode($data);

        if (empty($data['class']) || !class_exists($data['class'])) {
            throw new UnsupportedException();
        }

        $class_name = $data['class'];

        return new $class_name($data['payload']);
    }
}
```
