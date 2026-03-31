<?php

use Psr\Container\ContainerInterface;
use photopro\core\application\usecases\StorageService;
use Aws\S3\S3Client;

return [
    StorageService::class => function (ContainerInterface $c) {
        // 1. Création du client interne (pour l'upload depuis PHP vers S3 dans le réseau Docker)
        $internalClient = new S3Client([
            'version'                 => 'latest',
            'region'                  => $_ENV['S3_REGION'],
            'endpoint'                => $_ENV['S3_INTERNAL_ENDPOINT'],
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key'    => $_ENV['S3_ACCESS_KEY'],
                'secret' => $_ENV['S3_SECRET_KEY'],
            ],
        ]);

        // 2. Création du client externe (pour générer les URLs visibles par le navigateur)
        $externalClient = new S3Client([
            'version'                 => 'latest',
            'region'                  => $_ENV['S3_REGION'],
            'endpoint'                => $_ENV['S3_EXTERNAL_ENDPOINT'],
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key'    => $_ENV['S3_ACCESS_KEY'],
                'secret' => $_ENV['S3_SECRET_KEY'],
            ],
        ]);

        // 3. On retourne le service prêt à l'emploi avec le nom du bucket
        return new StorageService($internalClient, $externalClient, $_ENV['S3_BUCKET']);
    },
];