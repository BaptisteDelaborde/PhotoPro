<?php

namespace photopro\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use photopro\core\application\usecases\StorageService;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpInternalServerErrorException;

class UploadAction
{
    private StorageService $storageService;
    
    private const array ALLOWED_MIME = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif'
    ];
    
    private const int MAX_SIZE = 10 * 1024 * 1024;

    public function __construct(StorageService $storageService) {
        $this->storageService = $storageService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $photographer_id = $args['id'] ?? 'anonyme'; 
        
        $files = $request->getUploadedFiles();

        if (empty($files['photo'])) {
            throw new HttpBadRequestException($request, 'Pas de fichier présent dans la requête.');
        }

        $upload = $files['photo'];

        if ($upload->getError() !== UPLOAD_ERR_OK) {
            throw new HttpBadRequestException($request, 'Erreur technique lors de l\'upload : ' . $upload->getError());
        }

        $mimeType = $upload->getClientMediaType();
        if (!in_array($mimeType, self::ALLOWED_MIME, true)) {
            throw new HttpBadRequestException($request, 'Type de fichier non autorisé : ' . $mimeType);
        }

        if ($upload->getSize() > self::MAX_SIZE) {
            throw new HttpBadRequestException($request, 'Le fichier est trop volumineux (max 10 Mo).');
        }

        try {
            $key = $this->storageService->store($photographer_id, $upload->getStream(), $mimeType);
            
            $url = $this->storageService->getPresignedUrl($key);
            
        } catch (\Exception $e) {
            throw new HttpInternalServerErrorException($request, 'Erreur du serveur de stockage : ' . $e->getMessage());
        }

        $response->getBody()->write(json_encode([
            'message' => 'Image uploadée avec succès !',
            's3_key' => $key,
            'url' => $url
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }
}