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
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class AMQPSubscribeEventQueue implements SubscribeEventQueue
{
    /**
     * @var AMQPChannel
     */
    private $channel;

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
     * @var bool
     */
    private $declared = false;

    /**
     * @param AMQPChannel     $channel
     * @param Serializer      $serializer
     * @param LoggerInterface $logger
     * @param string          $queue_name
     */
    public function __construct(AMQPChannel $channel, Serializer $serializer, LoggerInterface $logger, $queue_name)
    {
        $this->channel = $channel;
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
        $message = $this->serializer->serialize($event);
        $this->declareQueue();
        $this->channel->basic_publish(new AMQPMessage($message), '', $this->queue_name);

        return true;
    }

    /**
     * Subscribe on event queue.
     *
     * @throws \ErrorException
     *
     * @param callable $handler
     */
    public function subscribe(callable $handler)
    {
        $this->handlers[] = $handler;

        // laze subscribe
        if (!$this->subscribed) {
            $this->declareQueue();
            $this->channel->basic_consume(
                $this->queue_name,
                '',
                false,
                true,
                false,
                false,
                function (AMQPMessage $message) {
                    $this->handle($message->body);
                }
            );

            $this->subscribed = true;
        }

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
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

    private function declareQueue()
    {
        // laze declare queue
        if (!$this->declared) {
            $this->channel->queue_declare($this->queue_name, false, false, false, false);
            $this->declared = true;
        }
    }

    /**
     * @param string $message
     */
    private function handle($message)
    {
        try {
            $event = $this->serializer->deserialize($message);
        } catch (\Exception $e) { // catch only deserialize exception
            // it's a critical error
            // it is necessary to react quickly to it
            $this->logger->critical('Failed denormalize a event in the AMQP queue', [$message, $e->getMessage()]);

            // try denormalize in later
            $this->declareQueue();
            $this->channel->basic_publish(new AMQPMessage($message), '', $this->queue_name);

            return; // no event for handle
        }

        foreach ($this->handlers as $handler) {
            call_user_func($handler, $event);
        }
    }
}
