<?php

namespace photopro\api\actions;

use photopro\api\providers\AuthnProviderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpUnauthorizedException;

class RefreshAction {

    private AuthnProviderInterface $authnProvider;

    public function __construct(AuthnProviderInterface $authnProvider) {
        $this->authnProvider = $authnProvider;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $authHeader = $request->getHeaderLine('Authorization');
        if (empty($authHeader) || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            throw new HttpUnauthorizedException($request, "Jeton de rafraîchissement manquant ou invalide.");
        }

        $tokenStr = $matches[1];

        try {
            [$authDTO, $profileDTO] = $this->authnProvider->refresh($tokenStr);

            $data = [
                'access_token' => $authDTO->accesToken,
                'refresh_token' => $authDTO->refreshToken,
                'profile' => [
                    'id' => $profileDTO->id,
                    'email' => $profileDTO->email,
                    'role' => $profileDTO->role
                ]
            ];

            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            throw new HttpUnauthorizedException($request, $e->getMessage());
        }
    }
}
