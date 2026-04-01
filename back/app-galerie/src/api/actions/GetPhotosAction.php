<?php

namespace photopro\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use photopro\core\application\ports\api\ServiceGalerieInterface;

class GetPhotosAction
{
    private ServiceGalerieInterface $serviceGalerie;

    public function __construct(ServiceGalerieInterface $serviceGalerie) {
        $this->serviceGalerie = $serviceGalerie;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $photographer_id = $args['id'];
        
        $photos = $this->serviceGalerie->getPhotos($photographer_id);
        
        $response->getBody()->write(json_encode([
            'photographer_id' => $photographer_id,
            'count' => count($photos),
            'photos' => $photos
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}