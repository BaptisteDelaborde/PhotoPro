<?php

namespace photopro\api\actions;

use photopro\core\application\ports\api\ServiceGalerieInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CreateGalerieAction
{
    private ServiceGalerieInterface $serviceGalerie;

    // L'injection de dépendance : Slim (via PHP-DI) va automatiquement nous fournir le Service
    public function __construct(ServiceGalerieInterface $serviceGalerie)
    {
        $this->serviceGalerie = $serviceGalerie;
    }

    // La méthode magique __invoke permet d'utiliser la classe comme une fonction
    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        // 1. Récupération des données JSON envoyées dans le body
        $data = $rq->getParsedBody();

        // Vérification basique des champs obligatoires
        if (!isset($data['title']) || !isset($data['layout']) || !isset($data['is_public'])) {
            $rs->getBody()->write(json_encode(['error' => 'Données manquantes (title, layout, is_public requises).']));
            return $rs->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        // NOTE : Normalement, le photographer_id est extrait du token JWT de connexion via un Middleware.
        // Pour l'instant, on suppose qu'il est envoyé dans le body ou on met une valeur par défaut pour tester.
        $photographerId = $data['photographer_id'] ?? 'uuid-photographe-test';

        try {
            // Appel au Use Case
            $galerieDTO = $this->serviceGalerie->createGalerie(
                $photographerId,
                $data['title'],
                $data['layout'],
                (bool) $data['is_public'],
                $data['description'] ?? null,
                $data['client_name'] ?? null,
                $data['client_email'] ?? null
            );

            // Construction de la réponse de succès
            $rs->getBody()->write(json_encode([
                'message' => 'Galerie créée avec succès',
                'galerie' => [
                    'id' => $galerieDTO->id,
                    'title' => $galerieDTO->title,
                    'is_public' => $galerieDTO->is_public,
                    'access_code' => $galerieDTO->access_code,
                    'access_url' => $galerieDTO->access_url
                ]
            ]));

            return $rs->withStatus(201)->withHeader('Content-Type', 'application/json');

        } catch (\InvalidArgumentException $e) {
            // Règle non respectée (ex: email manquant pour galerie privée)
            $rs->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $rs->withStatus(400)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $rs->getBody()->write(json_encode(['error' => 'Erreur interne du serveur lors de la création de la galerie.']));
            return $rs->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}