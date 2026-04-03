<?php

declare(strict_types=1);

namespace photopro\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use photopro\core\application\ports\api\ServiceGalerieInterface;

class GetPublicGaleriePhotosAction {

    public function __construct(private ServiceGalerieInterface $serviceGalerie) {}

    public function __invoke(Request $rq, Response $rs, array $args): Response
    {

        $galerieId = $args['id'];

        $photos = $this->serviceGalerie->getPhotosByGalerie($galerieId);


        $rs->getBody()->write(json_encode($photos));
        return $rs
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}