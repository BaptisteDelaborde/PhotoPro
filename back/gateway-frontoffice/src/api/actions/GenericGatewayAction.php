<?php

namespace gateway\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpInternalServerErrorException;

class GenericGatewayAction {
    private Client $galerieClient;
    private Client $authClient;

    public function __construct(Client $galerieClient, Client $authClient) {
        $this->galerieClient = $galerieClient;
        $this->authClient = $authClient;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $method = $request->getMethod();
        $originalPath = $request->getUri()->getPath();

        [$client, $path] = $this->resolveClientAndPath($originalPath);

        $headers = $request->getHeaders();
        unset($headers['Host']);
        unset($headers['Content-Length']);

        $options = [
            'query' => $request->getQueryParams(),
            'headers' => $headers,
            'http_errors' => true
        ];

        if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $parsedBody = $request->getParsedBody();
            if (!empty($parsedBody)) {
                $options['json'] = $parsedBody;
            } else {
                $body = $request->getBody();
                $body->rewind();
                if ($body->getSize() > 0) {
                    $options['body'] = $body->getContents();
                }
            }
        }

        try {
            $apiResponse = $client->request($method, $path, $options);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                if ($statusCode === 404) {
                    throw new HttpNotFoundException($request, "Ressource introuvable sur le service distant : $path");
                }
                $errorBody = $e->getResponse()->getBody()->getContents();
                $response->getBody()->write($errorBody);
                return $response
                    ->withStatus($statusCode)
                    ->withHeader('Content-Type', 'application/json');
            }
            throw new HttpInternalServerErrorException($request, "Erreur Gateway vers : $path", $e);
        }

        $response->getBody()->write($apiResponse->getBody()->getContents());
        return $response->withStatus($apiResponse->getStatusCode())
            ->withHeader('Content-Type', 'application/json');
    }

    /**
     * Retourne [Client, chemin adapté] selon le path entrant
     */
    private function resolveClientAndPath(string $path): array
    {
        // /auth/signin → /signin sur app-auth (pour le mode photographe mobile)
        // /auth/refresh → /refresh sur app-auth
        if (str_starts_with($path, '/auth/')) {
            return [$this->authClient, substr($path, 5)]; // supprime "/auth"
        }

        // Tout le reste → app-galerie (galeries, photos, commentaires, photographes)
        return [$this->galerieClient, $path];
    }
}
