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

    public function __construct(Client $galerieClient, Client $authClient){
        $this->galerieClient = $galerieClient;
        $this->authClient = $authClient;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $method = $request->getMethod();
        $path = '/' . ($args['routes'] ?? '');

        $client = $this->selectClient($path);

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
            }
            throw new HttpInternalServerErrorException($request, "Erreur Gateway vers : $path", $e);
        }
        $response->getBody()->write($apiResponse->getBody()->getContents());
        return $response->withStatus($apiResponse->getStatusCode())
            ->withHeader('Content-Type', 'application/json');
    }

    /**
     * Sélectionne le bon client par rapport au chemin
     */
    private function selectClient(string $path): Client
    {
        // app-auth
        if (str_starts_with($path, '/signin') || str_starts_with($path, '/signup') || str_starts_with($path, '/refresh') || str_starts_with($path, '/tokens')) {
            return $this->authClient;
        }

        //api_galerie
        return $this->galerieClient;
    }
}
