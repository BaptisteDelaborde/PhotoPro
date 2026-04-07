<?php

namespace photopro\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use PDO;

class LinkPhotoToGalerieAction
{
    private PDO $pdo;

    public function __construct(ContainerInterface $container)
    {
        $this->pdo = $container->get(PDO::class);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $photoId = $args['photo_id'];
        $data = $request->getParsedBody() ?? [];
        
        $galerieId = $data['galerie_id'] ?? null;
        $action = $data['action'] ?? 'add';

        if (!$galerieId) {
            $response->getBody()->write(json_encode(['error' => 'galerie_id manquant']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            if ($action === 'add') {
                $stmt = $this->pdo->prepare("INSERT INTO gallery_photos (gallery_id, photo_id) VALUES (:g, :p) ON CONFLICT DO NOTHING");
            } else {
                $stmt = $this->pdo->prepare("DELETE FROM gallery_photos WHERE gallery_id = :g AND photo_id = :p");
            }
            $stmt->execute(['g' => $galerieId, 'p' => $photoId]);

            $response->getBody()->write(json_encode(['message' => 'Liaison mise à jour']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Erreur BDD : ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}