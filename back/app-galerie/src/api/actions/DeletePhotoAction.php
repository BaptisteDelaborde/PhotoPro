<?php

namespace photopro\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use photopro\core\application\ports\api\ServiceGalerieInterface;
use Slim\Exception\HttpNotFoundException;

class DeletePhotoAction
{
    private ServiceGalerieInterface $serviceGalerie;

    public function __construct(ServiceGalerieInterface $serviceGalerie) {
        $this->serviceGalerie = $serviceGalerie;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $photo_id = $args['photo_id'];

        try {
            $s3Key = $this->serviceGalerie->deletePhoto($photo_id);
        } catch (\Exception $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        }

        $response->getBody()->write(json_encode([
            'message' => 'Photo supprimée de la base de données.',
            's3_key' => $s3Key
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}