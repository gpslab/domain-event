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
use GpsLab\Domain\Event\Queue\Serializer\Serializer;
use Psr\Log\LoggerInterface;
use Superbalist\PubSub\Redis\RedisPubSubAdapter;

class PredisSubscribeEventQueue implements SubscribeEventQueue
{
    /**
     * @var RedisPubSubAdapter
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
     * @var callable[]
     */
    private $handlers = [];

    /**
     * @var string
     */
    private $queue_name = '';

    /**
     * @var bool
     */
    private $subscribed = false;

    /**
     * @param RedisPubSubAdapter $client
     * @param Serializer         $serializer
     * @param LoggerInterface    $logger
     * @param string             $queue_name
     */
    public function __construct(
        RedisPubSubAdapter $client,
        Serializer $serializer,
        LoggerInterface $logger,
        $queue_name
    ) {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->queue_name = $queue_name;
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
        $massage = $this->serializer->serialize($event);
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
        $this->handlers[] = $handler;

        // laze subscribe
        if (!$this->subscribed) {
            $this->client->subscribe($this->queue_name, function ($message) {
                $this->handle($message);
            });
            $this->subscribed = true;
        }
    }

    /**
     * Unsubscribe on event queue.
     *
     * @param callable $handler
     *
     * @return bool
     */
    public function unsubscribe(callable $handler)
    {
        $index = array_search($handler, $this->handlers);

        if ($index === false) {
            return false;
        }

        unset($this->handlers[$index]);

        return true;
    }

    /**
     * @param mixed $message
     */
    private function handle($message)
    {
        try {
            $event = $this->serializer->deserialize($message);
        } catch (\Exception $e) { // catch only deserialize exception
            // it's a critical error
            // it is necessary to react quickly to it
            $this->logger->critical(
                'Failed denormalize a event in the Redis queue',
                [$message, $e->getMessage()]
            );

            // try denormalize in later
            $this->client->publish($this->queue_name, $message);

            return; // no event for handle
        }

        foreach ($this->handlers as $handler) {
            call_user_func($handler, $event);
        }
    }
}
