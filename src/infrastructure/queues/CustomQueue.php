<?php

declare(strict_types=1);

namespace omarinina\infrastructure\queues;

use Interop\Amqp\AmqpConsumer;
use Interop\Amqp\AmqpMessage;
use Interop\Queue\Exception\SubscriptionConsumerNotSupportedException;
use yii\queue\amqp_interop\Queue;

class CustomQueue extends Queue
{
    /**
     * Listens amqp-queue and runs new jobs.
     * @return void
     * @throws SubscriptionConsumerNotSupportedException
     */
    public function listen() : void
    {
        $this->open();
        $this->setupBroker();

        $queue = $this->context->createQueue($this->queueName);
        $consumer = $this->context->createConsumer($queue);
        $consumer->setConsumerTag('');

        $callback = function (AmqpMessage $message, AmqpConsumer $consumer) {
            if ($message->isRedelivered()) {
                $consumer->acknowledge($message);

                $this->redeliver($message);

                return true;
            }

            $ttr = $message->getProperty(self::TTR);
            $attempt = $message->getProperty(self::ATTEMPT, 1);

            if ($this->handleMessage($message->getMessageId(), $message->getBody(), $ttr, $attempt)) {
                $consumer->acknowledge($message);
            } else {
                $consumer->acknowledge($message);

                $this->redeliver($message);
            }

            return true;
        };

        $subscriptionConsumer = $this->context->createSubscriptionConsumer();
        $subscriptionConsumer->subscribe($consumer, $callback);
        $subscriptionConsumer->consume();
    }
}