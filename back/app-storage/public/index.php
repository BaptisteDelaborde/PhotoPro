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

$app->delete('/delete', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $s3Key = $data['s3_key'] ?? null;

    if (empty($s3Key)) {
        $response->getBody()->write(json_encode(['error' => 'La clé s3_key est manquante']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $s3Service = $this->get(S3Service::class);
    
    try {
        $s3Service->deleteFile($s3Key);
        
        $response->getBody()->write(json_encode([
            'message' => 'Fichier physiquement supprimé avec succès',
            's3_key' => $s3Key
        ]));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        
    } catch (\Exception $e) {
        $response->getBody()->write(json_encode(['error' => 'Erreur S3 : ' . $e->getMessage()]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

$app->run();