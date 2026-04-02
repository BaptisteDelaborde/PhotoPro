<?php

use DI\Container;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use photopro\storage\services\S3Service;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();
AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

$container->set(S3Service::class, function () {
    return new S3Service();
});

$app->post('/upload', function (Request $request, Response $response) {
    $uploadedFiles = $request->getUploadedFiles();

    if (empty($uploadedFiles['photo'])) {
        $response->getBody()->write(json_encode(['error' => 'Aucun fichier "photo" fourni']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $photo = $uploadedFiles['photo'];
    if ($photo->getError() !== UPLOAD_ERR_OK) {
        $response->getBody()->write(json_encode(['error' => 'Erreur lors de l\'upload physique']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $s3Service = $this->get(S3Service::class);
    
    $pathInfo = pathinfo($photo->getClientFilename());
    $extension = $pathInfo['extension'] ?? 'jpg';

    $result = $s3Service->uploadFile(
        $photo->getStream(), 
        $photo->getClientMediaType(), 
        $extension
    );

    $responseData = [
        'file_name' => $photo->getClientFilename(),
        'mime_type' => $photo->getClientMediaType(),
        'file_size' => $photo->getSize(),
        's3_key'    => $result['s3_key'],
        'url'       => $result['url']
    ];

    $response->getBody()->write(json_encode($responseData));
    return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
});

$app->run();