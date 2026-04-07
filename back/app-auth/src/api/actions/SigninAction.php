<?php

namespace photopro\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use photopro\api\providers\AuthnProviderInterface;
use photopro\core\application\ports\api\CredentialsDTO;

class SigninAction
{
    public function __construct(
        private readonly AuthnProviderInterface $authnProvider
    ) {
    }

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';

            if (($email === '') OR ($password === '')) {
                throw new \Exception("Email ou mot de passe non fourni");
            }
            $credentials = new CredentialsDTO($data['email'], $data['password']);
            $resSignIn = $this->authnProvider->signin($credentials);
            $authDTO = $resSignIn[0];
            $profile = $resSignIn[1]; // Ce ProfileDTO doit maintenant contenir pseudo, firstName, etc.

            $res = [
                'payload' => [
                    'access_token' => $authDTO->accesToken,
                    'refresh_token' => $authDTO->refreshToken,
                ],
                'profile' => [
                    'id' => $profile->id,
                    'email' => $profile->email,
                    'pseudo' => $profile->pseudo,
                    'first_name' => $profile->firstName,
                    'last_name' => $profile->lastName,
                    'role' => $profile->role
                ]
            ];
            $response->getBody()->write(json_encode($res, JSON_PRETTY_PRINT));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }
    }
}
