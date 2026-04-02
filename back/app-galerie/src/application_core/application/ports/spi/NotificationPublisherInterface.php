<?php

namespace photopro\core\application\ports\spi;

interface NotificationPublisherInterface
{
    public function publish(string $event, array $payload): void;
}
