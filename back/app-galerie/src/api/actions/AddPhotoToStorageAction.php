<?php

namespace photopro\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use GuzzleHttp\Client;
use PDO;
use Ramsey\Uuid\Uuid;

class AddPhotoToStorageAction
{
    private PDO $pdo;

    // 🌟 CORRECTION : On accepte le Container, et on va chercher PDO dedans
    public function __construct(ContainerInterface $container)
    {
        $this->pdo = $container->get(PDO::class);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $photographerId = $args['id'];
        $uploadedFiles = $request->getUploadedFiles();

        if (empty($uploadedFiles['photo'])) {
            $response->getBody()->write(json_encode(['error' => 'Aucun fichier photo fourni']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $uploadedFile = $uploadedFiles['photo'];

        $client = new Client([
            'base_uri' => (string)(getenv('STORAGE_API_URL') ?: 'http://api_storage:80')
        ]);

        try {
            $storageRes = $client->post('/upload', [
                'multipart' => [
                    [
                        'name'     => 'photo',
                        'contents' => $uploadedFile->getStream()->detach(),
                        'filename' => $uploadedFile->getClientFilename(),
                        'headers'  => ['Content-Type' => $uploadedFile->getClientMediaType()]
                    ]
                ]
            ]);
            
            $storageData = json_decode($storageRes->getBody()->getContents(), true);
            $storageUrl = $storageData['url'] ?? null;

            if (!$storageUrl) {
                throw new \Exception("L'URL S3 n'a pas été retournée par le Storage");
            }

            $photoId = Uuid::uuid4()->toString();
            
            $stmt = $this->pdo->prepare("
                INSERT INTO photos (id, photographer_id, file_name, mime_type, file_size, storage_url) 
                VALUES (:id, :photographer_id, :file_name, :mime_type, :file_size, :storage_url)
            ");
            
            $stmt->execute([
                'id' => $photoId,
                'photographer_id' => $photographerId,
                'file_name' => $uploadedFile->getClientFilename(),
                'mime_type' => $uploadedFile->getClientMediaType(),
                'file_size' => $uploadedFile->getSize(),
                'storage_url' => $storageUrl
            ]);

            $response->getBody()->write(json_encode([
                'message' => 'Photo ajoutée au stockage global avec succès',
                'photo_id' => $photoId,
                'url' => $storageUrl
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Erreur lors de la sauvegarde : ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}