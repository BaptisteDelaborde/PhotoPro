<?php

namespace photopro\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use photopro\core\application\usecases\StorageService;
use photopro\core\application\ports\api\ServiceGalerieInterface;
use photopro\core\application\usecases\ServiceGalerie; // Import du service
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpInternalServerErrorException;

class UploadAction
{
    private StorageService $storageService;
    private ServiceGalerieInterface $serviceGalerie; // On utilise l'interface
    
    private const array ALLOWED_MIME = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif'
    ];
    
    private const int MAX_SIZE = 10 * 1024 * 1024;

    // Injection des DEUX services dans le constructeur
    public function __construct(StorageService $storageService, ServiceGalerie $serviceGalerie) {
        $this->storageService = $storageService;
        $this->serviceGalerie = $serviceGalerie;
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
            // 1. Envoi S3
            $key = $this->storageService->store($photographer_id, $upload->getStream(), $mimeType);
            $url = $this->storageService->getPresignedUrl($key);
            
            // 2. Sauvegarde en Base de données (NOUVEAU !)
            $photo = $this->serviceGalerie->ajouterPhoto(
                $photographer_id,
                $upload->getClientFilename(),
                $mimeType,
                $upload->getSize(),
                $key
            );
            
        } catch (\Exception $e) {
            throw new HttpInternalServerErrorException($request, 'Erreur technique : ' . $e->getMessage());
        }

        // Nouveau message JSON avec l'ID de la photo
        $response->getBody()->write(json_encode([
            'message' => 'Image uploadée et sauvegardée avec succès !',
            'photo_id' => $photo->getId(),
            's3_key' => $key,
            'url' => $url
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }
}