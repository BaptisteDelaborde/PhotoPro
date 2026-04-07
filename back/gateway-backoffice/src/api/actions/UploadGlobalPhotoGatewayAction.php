<?php

namespace gateway\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Client;

class UploadGlobalPhotoGatewayAction
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $photographeId = $args['id'];
        $uploadedFiles = $request->getUploadedFiles();

        if (empty($uploadedFiles['photo'])) {
            $response->getBody()->write(json_encode(['error' => 'Aucun fichier photo fourni à la Gateway']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $uploadedFile = $uploadedFiles['photo'];

        // 🌟 CORRECTION : On récupère le Token envoyé par Vue.js
        $authHeader = $request->getHeaderLine('Authorization');

        $client = new Client([
            'base_uri' => (string)(getenv('GALERIE_API_URL') ?: 'http://api_galerie:80')
        ]);

        try {
            // On transfère le fichier vers l'API Galerie
            $res = $client->post('/photographes/' . $photographeId . '/photos', [
                // 🌟 CORRECTION : On glisse le Token dans le colis pour passer la douane du microservice !
                'headers' => [
                    'Authorization' => $authHeader
                ],
                'multipart' => [
                    [
                        'name'     => 'photo',
                        'contents' => $uploadedFile->getStream()->detach(),
                        'filename' => $uploadedFile->getClientFilename(),
                        'headers'  => ['Content-Type' => $uploadedFile->getClientMediaType()]
                    ]
                ]
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