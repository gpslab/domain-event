<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2016, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Queue;

use GpsLab\Domain\Event\Event;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Serializer;

class PredisUniqueEventQueue implements EventQueueInterface
{
    const LIST_KEY = 'unique_events';
    const FORMAT = 'predis';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Client $client
     * @param Serializer $serializer
     * @param LoggerInterface $logger
     */
    public function __construct(Client $client, Serializer $serializer, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * Push event to queue.
     *
     * @param Event $event
     *
     * @return bool
     */
    public function push(Event $event)
    {
        $value = $this->serializer->normalize($event, self::FORMAT);

        // remove already exists value to remove duplication
        $this->client->lrem(self::LIST_KEY, 0, $value);

        return (bool) $this->client->rpush(self::LIST_KEY, [$value]);
    }

    /**
     * Pop event from queue. Return NULL if queue is empty.
     *
     * @return Event|null
     */
    public function pop()
    {
        $value = $this->client->lpop(self::LIST_KEY);

        if (!$value) {
            return null;
        }

        try {
            return $this->serializer->denormalize($value, Event::class, self::FORMAT);
        } catch (\Exception $e) {
            // it's a critical error
            // it is necessary to react quickly to it
            $this->logger->critical('Failed denormalize a event in the Redis queue', [$value, $e->getMessage()]);

            // try denormalize in later
            $this->client->rpush(self::LIST_KEY, [$value]);

            return null;
        }
    }
}
