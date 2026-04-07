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
        $photographerId = $args['id'];
        $photoId = $args['photo_id'];
        $data = $request->getParsedBody() ?? [];
        
        $galerieId = !empty($data['galerie_id']) ? $data['galerie_id'] : null;

        try {
            $stmt = $this->pdo->prepare("
                UPDATE photos 
                SET galerie_id = :galerie_id 
                WHERE id = :id AND photographer_id = :photographer_id
            ");
            
            $stmt->execute([
                'galerie_id' => $galerieId,
                'id' => $photoId,
                'photographer_id' => $photographerId
            ]);

            $response->getBody()->write(json_encode([
                'message' => 'Statut de la photo mis à jour',
                'photo_id' => $photoId,
                'galerie_id' => $galerieId
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Erreur BDD : ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}