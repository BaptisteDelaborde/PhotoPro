<?php
namespace photopro\api\actions;

use photopro\core\application\ports\api\ServiceGalerieInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CreateGalerieAction
{
    private ServiceGalerieInterface $serviceGalerie;

    public function __construct(ServiceGalerieInterface $serviceGalerie)
    {
        $this->serviceGalerie = $serviceGalerie;
    }

    public function __invoke(Request $rq, Response $rs): Response
    {
        // RÉCUPÉRATION DU PROFIL (Injecté par le Middleware)
        $userProfile = $rq->getAttribute('user_profile');

        if (!$userProfile) {
            $rs->getBody()->write(json_encode(['error' => 'Non autorisé']));
            return $rs->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $data = $rq->getParsedBody();

        // Validation minimale
        if (!isset($data['title'], $data['layout'], $data['is_public'])) {
            $rs->getBody()->write(json_encode(['error' => 'Champs title, layout, is_public requis']));
            return $rs->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        try {
            // On utilise le VRAI ID du photographe issu du token
            $galerieDTO = $this->serviceGalerie->createGalerie(
                $userProfile->id,
                $data['title'],
                $data['layout'],
                (bool) $data['is_public'],
                $data['description'] ?? null,
                $data['client_name'] ?? null,
                $data['client_email'] ?? null
            );

            $rs->getBody()->write(json_encode($galerieDTO));
            return $rs->withStatus(201)->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            // Debug : on affiche l'erreur réelle pour comprendre si SQL bloque
            $rs->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $rs->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}