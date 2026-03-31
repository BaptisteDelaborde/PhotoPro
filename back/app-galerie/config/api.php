<?php

use Psr\Container\ContainerInterface;
use photopro\api\actions\UploadAction;
use photopro\core\application\usecases\StorageService;

return [
    UploadAction::class => function (ContainerInterface $c) {
        return new UploadAction($c->get(StorageService::class));
    },
];