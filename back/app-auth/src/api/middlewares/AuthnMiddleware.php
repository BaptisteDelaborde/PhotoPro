<?php

namespace photopro\api\middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use photopro\api\providers\JWTManager;
use Slim\Psr7\Response as SlimResponse;

class AuthnMiddleware {
    private JWTManager $jwtManager;

    public function __construct(JWTManager $jwtManager) {
        $this->jwtManager = $jwtManager;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response {
        $header = $request->getHeaderLine('Authorization');
        if (empty($header)) {
            return $this->errorResponse("Token d'authentification manquant", 401);
        }

        $token = str_replace('Bearer ', '', $header);

        try {
            $decoded = $this->jwtManager->decodeToken($token);

            if (!isset($decoded['type']) || $decoded['type'] !== 'access') {
                return $this->errorResponse("Token invalide (type attendu : access)", 401);
            }
            
            $request = $request->withAttribute('user', $decoded);
            
            return $handler->handle($request);
            
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 401);
        }
    }

    private function errorResponse(string $message, int $status): Response {
        $response = new SlimResponse();
        $response->getBody()->write(json_encode(["error" => $message], JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}
