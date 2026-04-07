<?php

namespace photopro\api\actions;

use photopro\core\application\ports\api\ServiceGalerieInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DeleteGalerieAction
{
    public function __construct(private ServiceGalerieInterface $serviceGalerie) {}

    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        $galerieId = $args['galerie_id'] ?? $args['id'];
        
        $userProfile = $rq->getAttribute('user_profile');

        $galerieDTO = $this->serviceGalerie->getGalerie($galerieId);

        if ($galerieDTO->photographer_id !== $userProfile->id) {
            return $rs->withStatus(403);
        }

        $this->serviceGalerie->deleteGalerie($galerieId);
        
        return $rs->withStatus(204);
    }
}