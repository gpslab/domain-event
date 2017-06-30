<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Domain\Event\Queue\Subscribe;

use GpsLab\Domain\Event\Event;
use Psr\Log\LoggerInterface;
use Superbalist\PubSub\Redis\RedisPubSubAdapter;
use Symfony\Component\Serializer\SerializerInterface;

class PredisSubscribeEventQueue implements SubscribeEventQueue
{
    const DEFAULT_FORMAT = 'predis';

    /**
     * @var RedisPubSubAdapter
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
     * @param RedisPubSubAdapter  $client
     * @param SerializerInterface $serializer
     * @param LoggerInterface     $logger
     * @param string              $queue_name
     * @param string|null         $format
     */
    public function __construct(
        RedisPubSubAdapter $client,
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
     * Publish event to queue.
     *
     * @param Event $event
     *
     * @return bool
     */
    public function publish(Event $event)
    {
        $massage = $this->serializer->serialize($event, $this->format);
        $this->client->publish($this->queue_name, $massage);

        return true;
    }

    /**
     * Subscribe on event queue.
     *
     * @param callable $handler
     */
    public function subscribe(callable $handler)
    {
        $this->client->subscribe($this->queue_name, function ($message) use ($handler) {
            try {
                $event = $this->serializer->deserialize($message, Event::class, $this->format);
            } catch (\Exception $e) { // catch only deserialize exception
                // it's a critical error
                // it is necessary to react quickly to it
                $this->logger->critical(
                    'Failed denormalize a event in the Redis queue',
                    [$message, $e->getMessage()]
                );

                // try denormalize in later
                $this->client->publish($this->queue_name, $message);
            }

            if (isset($event)) {
                call_user_func($handler, $event);
            }
        });
    }
}
