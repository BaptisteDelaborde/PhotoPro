<?php

namespace photopro\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use PDO;

class GetPhotosAction
{
    private PDO $pdo;

    public function __construct(ContainerInterface $container) {
        $this->pdo = $container->get(PDO::class);
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $photographer_id = $args['id'] ?? null;
        $galerie_id = $args['galerie_id'];
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.* FROM photos p
                INNER JOIN gallery_photos gp ON p.id = gp.photo_id
                WHERE gp.gallery_id = :galerie_id
                ORDER BY gp.added_at DESC
            ");
            
            $stmt->execute(['galerie_id' => $galerie_id]);
            $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $response->getBody()->write(json_encode([
                'photographer_id' => $photographer_id,
                'galerie_id' => $galerie_id,
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