<?php

namespace gateway\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Client;
use Slim\Exception\HttpInternalServerErrorException;

class DeletePhotoGatewayAction
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $photographerId = $args['id'];
        $galerieId = $args['galerie_id'];
        $photoId = $args['photo_id'];

        $client = new Client();
        $authHeader = $request->getHeaderLine('Authorization');

        try {
            // ========================================================
            // ÉTAPE 1 : SUPPRESSION DANS L'API GALERIE (BDD)
            // ========================================================
            $galerieResponse = $client->delete("http://api_galerie:80/photographes/{$photographerId}/galeries/{$galerieId}/photos/{$photoId}", [
                'headers' => [
                    'Authorization' => $authHeader,
                    'Accept'        => 'application/json'
                ]
            ]);

            $galerieData = json_decode($galerieResponse->getBody()->getContents(), true);
            $s3Key = $galerieData['s3_key'] ?? null;

            if (!$s3Key) {
                throw new \Exception("L'API Galerie n'a pas renvoyé la s3_key.");
            }

            // ========================================================
            // ÉTAPE 2 : SUPPRESSION DANS L'API STORAGE (S3)
            // ========================================================
            $client->delete("http://api_storage:80/delete", [
                'json' => ['s3_key' => $s3Key]
            ]);

            // ========================================================
            // ÉTAPE 3 : RÉPONSE AU FRONT-END
            // ========================================================
            $response->getBody()->write(json_encode([
                'message' => 'Photo totalement supprimée (BDD + Stockage S3)'
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $errorMsg = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            throw new HttpInternalServerErrorException($request, "Erreur lors de l'appel interne : " . $errorMsg);
        } catch (\Exception $e) {
            throw new HttpInternalServerErrorException($request, "Erreur Gateway : " . $e->getMessage());
        }
    }
}