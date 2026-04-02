<?php
declare(strict_types=1);

namespace photopro\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetPublicGaleriesAction {

    public function __invoke(Request $request, Response $response, array $args): Response {
        $galeriesPubliques = [
            [
                "id" => "1",
                "titre" => "Mariage de Sophie & Marc",
                "photo_couverture" => "sample-1.jpg",
                "status" => "public"
            ],
            [
                "id" => "2",
                "titre" => "Shooting Nature en Forêt",
                "photo_couverture" => "sample-2.jpg",
                "status" => "public"
            ]
        ];

        $payload = json_encode($galeriesPubliques);

        $response->getBody()->write($payload);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
