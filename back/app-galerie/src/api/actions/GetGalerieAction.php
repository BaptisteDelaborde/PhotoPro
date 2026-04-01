<?php

namespace photopro\api\actions;

use photopro\core\application\ports\api\ServiceGalerieInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetGalerieAction
{
    public function __construct(private ServiceGalerieInterface $serviceGalerie) {}

    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        $id = $args['id'];
        $userProfile = $rq->getAttribute('user_profile');
        $galerieDTO = $this->serviceGalerie->getGalerie($id);
        if ($galerieDTO->photographer_id !== $userProfile->id) {
            return $rs->withStatus(403);
        }

        if (!$galerieDTO) {
            return $rs->withStatus(404);
        }

        $rs->getBody()->write(json_encode($galerieDTO));
        return $rs->withHeader('Content-Type', 'application/json');
    }
}
