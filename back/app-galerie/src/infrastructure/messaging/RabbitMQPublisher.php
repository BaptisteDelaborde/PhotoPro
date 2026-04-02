<?php

namespace photopro\infra\messaging;

use photopro\core\application\ports\spi\NotificationPublisherInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQPublisher implements NotificationPublisherInterface
{
    private string $host;
    private int $port;
    private string $user;
    private string $password;
    private string $exchangeName;

    public function __construct(
        string $host = 'rabbitmq',
        int $port = 5672,
        string $user = 'photo',
        string $password = 'photo',
        string $exchangeName = 'galerie.events'
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->exchangeName = $exchangeName;
    }

    public function publish(string $event, array $payload): void
    {
        try {
            $connection = new AMQPStreamConnection(
                $this->host,
                $this->port,
                $this->user,
                $this->password
            );

            $channel = $connection->channel();

            $channel->exchange_declare($this->exchangeName, 'fanout', false, true, false);

            $body = json_encode(array_merge(['event' => $event], $payload));
            $message = new AMQPMessage($body, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

            $channel->basic_publish($message, $this->exchangeName);

            $channel->close();
            $connection->close();
        } catch (\Throwable $e) {
            error_log('[RabbitMQPublisher] Erreur publication : ' . $e->getMessage());
        }
    }
}
