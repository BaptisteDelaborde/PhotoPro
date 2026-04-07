<?php

namespace gateway\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Client;

class UpdateGalerieGatewayAction
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $galerieId = $args['galerie_id'];
        $data = $request->getParsedBody();

        $client = new Client([
            'base_uri' => (string)(getenv('GALERIE_API_URL') ?: 'http://api_galerie:80')
        ]);

        try {

            $res = $client->put('/galeries/' . $galerieId, [
                'json' => $data
            ]);

            $response->getBody()->write($res->getBody()->getContents());
            return $response->withHeader('Content-Type', 'application/json')->withStatus($res->getStatusCode());
            
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $status = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 500;
            $errorBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : json_encode(['error' => 'Erreur Gateway']);
            
            $response->getBody()->write($errorBody);
            return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
        }
    }
}