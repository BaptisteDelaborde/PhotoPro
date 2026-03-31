<?php

namespace photopro\api\middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Response as SlimResponse;

class AuthzMiddleware {
    private int $requiredRole;

    public function __construct(int $requiredRole) {
        $this->requiredRole = $requiredRole;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response {
        $user = $request->getAttribute('user');

        if (!$user || !isset($user['data'])) {
            return $this->errorResponse("Informations de profil manquantes dans le token", 403);
        }

        $data = (array) $user['data'];

        if (!isset($data['role'])) {
            return $this->errorResponse("Rôle introuvable dans le token", 403);
        }

        $userRole = (int) $data['role'];

        if ($userRole < $this->requiredRole) {
            return $this->errorResponse("Accès refusé : privilèges insuffisants", 403);
        }

        return $handler->handle($request);
    }

    private function errorResponse(string $message, int $status): Response {
        $response = new SlimResponse();
        $response->getBody()->write(json_encode(["error" => $message], JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}
