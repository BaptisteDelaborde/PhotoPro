<?php

namespace photopro\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use PDO;

class GetPhotographerPhotosAction
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

        try {
            $stmt = $this->pdo->prepare("
                SELECT p.id, p.title, p.file_name, p.mime_type, p.file_size, p.storage_url, p.uploaded_at,
                    (SELECT COUNT(*) FROM gallery_photos gp WHERE gp.photo_id = p.id) as in_galleries_count
                FROM photos p
                WHERE p.photographer_id = :photographer_id 
                ORDER BY p.uploaded_at DESC
            ");
            
            $stmt->execute(['photographer_id' => $photographerId]);
            $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response->getBody()->write(json_encode([
                'photographer_id' => $photographerId,
                'count' => count($photos),
                'photos' => $photos
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Erreur BDD : ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}