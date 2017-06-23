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
use Symfony\Component\Serializer\SerializerInterface;

class PredisUniqueEventQueue implements EventQueue
{
    const DEFAULT_FORMAT = PredisEventQueue::DEFAULT_FORMAT;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $queue_name = '';

    /**
     * @var string
     */
    private $format = '';

    /**
     * @param Client              $client
     * @param SerializerInterface $serializer
     * @param LoggerInterface     $logger
     * @param string              $queue_name
     * @param string|null         $format
     */
    public function __construct(
        Client $client,
        SerializerInterface $serializer,
        LoggerInterface $logger,
        $queue_name,
        $format = null
    ) {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->queue_name = $queue_name;
        $this->format = $format ?: self::DEFAULT_FORMAT;
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
        $value = $this->serializer->serialize($event, $this->format);

        // remove already exists value to remove duplication
        $this->client->lrem($this->queue_name, 0, $value);

        return (bool) $this->client->rpush($this->queue_name, [$value]);
    }

    /**
     * Pop event from queue. Return NULL if queue is empty.
     *
     * @return Event|null
     */
    public function pop()
    {
        $value = $this->client->lpop($this->queue_name);

        if (!$value) {
            return null;
        }

        try {
            return $this->serializer->deserialize($value, Event::class, $this->format);
        } catch (\Exception $e) {
            // it's a critical error
            // it is necessary to react quickly to it
            $this->logger->critical('Failed deserialize a event in the Redis queue', [$value, $e->getMessage()]);

            // try denormalize in later
            $this->client->rpush($this->queue_name, [$value]);

            return null;
        }
    }
}
