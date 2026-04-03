<?php

namespace gateway\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Client;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpInternalServerErrorException;

class UploadPhotoGatewayAction
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $photographerId = $args['id'];
        $galerieId = $args['galerie_id'];

        $uploadedFiles = $request->getUploadedFiles();
        if (empty($uploadedFiles['photo'])) {
            throw new HttpBadRequestException($request, "Aucun fichier 'photo' fourni à la Gateway.");
        }
        $photo = $uploadedFiles['photo'];

        $client = new Client();

        try {
            // ========================================================
            // ÉTAPE 1 : ENVOI AU MICROSERVICE STORAGE
            // ========================================================
            // On convertit le fichier Psr7 UploadedFile en ressource pour Guzzle
            $stream = $photo->getStream()->detach();

            $storageResponse = $client->post('http://api_storage:80/upload', [
                'multipart' => [
                    [
                        'name'     => 'photo',
                        'contents' => $stream,
                        'filename' => $photo->getClientFilename(),
                        'headers'  => ['Content-Type' => $photo->getClientMediaType()]
                    ]
                ]
            ]);

            $storageData = json_decode($storageResponse->getBody()->getContents(), true);

            if (!isset($storageData['s3_key'])) {
                throw new \Exception("Le service Storage n'a pas renvoyé de clé S3.");
            }

            // ========================================================
            // ÉTAPE 2 : ENVOI AU MICROSERVICE GALERIE
            // ========================================================
            // On prépare le JSON à envoyer à l'API Galerie
$galeriePayload = [
                'file_name' => $storageData['file_name'],
                'mime_type' => $storageData['mime_type'],
                'file_size' => $storageData['file_size'],
                's3_key'    => $storageData['s3_key']
            ];

            // 🌟 NOUVEAU : On récupère le token d'authentification de l'utilisateur
            $authHeader = $request->getHeaderLine('Authorization');

            // On ajoute l'en-tête (headers) dans l'appel Guzzle
            $galerieResponse = $client->post("http://api_galerie:80/photographes/{$photographerId}/galeries/{$galerieId}/photos", [
                'json' => $galeriePayload,
                'headers' => [
                    'Authorization' => $authHeader,
                    'Accept'        => 'application/json'
                ]
            ]);

            // ========================================================
            // ÉTAPE 3 : RÉPONSE FINALE AU CLIENT FRONT-END
            // ========================================================
$rawGalerieResponse = $galerieResponse->getBody()->getContents();
            $finalData = json_decode($rawGalerieResponse, true);
            
            // Si le JSON est invalide, on renvoie une erreur avec le contenu brut
            if (json_last_error() !== JSON_ERROR_NONE) {
                 throw new \Exception("L'API Galerie a renvoyé une réponse invalide (non-JSON) : " . $rawGalerieResponse);
            }

            // On fusionne les données utiles pour le Front
            $responseBody = array_merge($finalData, ['url' => $storageData['url']]);

            $response->getBody()->write(json_encode($responseBody));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Si l'API renvoie un code 4xx ou 5xx
            $errorMsg = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            throw new HttpInternalServerErrorException($request, "Erreur lors de l'appel interne (Guzzle) : " . $errorMsg);
        } catch (\Exception $e) {
            throw new HttpInternalServerErrorException($request, "Erreur Gateway : " . $e->getMessage());
        }
    }
}