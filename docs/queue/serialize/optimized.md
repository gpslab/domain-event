Optimized Symfony serializer
============================

Example Serializer/Deserializer for event `ArticleRenamedEvent`:

```php
use GpsLab\Domain\Event\Event;
use GpsLab\Domain\Event\Queue\Pull\PredisPullEventQueue;
use Symfony\Component\Serializer\Exception\UnsupportedException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ArticleRenamedEventSerializer implements NormalizerInterface, DenormalizerInterface
{
    const PATTERN = 'ArticleRenamed;%d;%d;%s';
    const REGEXP = '/^
            ArticleRenamed;     # event type
            (?<article_id>\d+); # article id
            (?<editor_id>\d+);  # editor of the change
            (?<new_name>.+      # new article name
        $/x';

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ArticleRenamedEvent && $format == PredisPullEventQueue::FORMAT;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        return sprintf(
            self::PATTERN,
            $object->articleId()->value(),
            $object->editorId()->value(),
            $object->newName()
        );
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (!preg_match(self::REGEXP, $data, $match)) {
            throw new UnsupportedException();
        }

        return new ArticleRenamedEvent(
            new ArticleId($match['article_id']),
            new EditorId($match['editor_id']),
            $match['new_name']
        );
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return
            $format === PredisEventQueue::FORMAT &&
            $type === Event::class &&
            preg_match(self::REGEXP, $data)
        ;
    }
}
```

If the Editor with ID `123` changes the title of the Article with ID `456`, to `Переход от монолитной архитектуры к
распределенной`, then we get the following serialized string:

```
ArticleRenamed;456;123;Переход от монолитной архитектуры к распределенной
```

In [JSON](https://en.wikipedia.org/wiki/JSON) format, this event would look like this:

```json
{
    "type": "RenameArticle",
    "article_id": 456,
    "editor_id": 123,
    "new_name": "\u041f\u0435\u0440\u0435\u0445\u043e\u0434 \u043e\u0442 \u043c\u043e\u043d\u043e\u043b\u0438\u0442\u043d\u043e\u0439 \u0430\u0440\u0445\u0438\u0442\u0435\u043a\u0442\u0443\u0440\u044b \u043a \u0440\u0430\u0441\u043f\u0440\u0435\u0434\u0435\u043b\u0435\u043d\u043d\u043e\u0439"
}
```

Much larger, is not it?

You are not required to use this particular serialization method. You have the right to choose any format you like.
