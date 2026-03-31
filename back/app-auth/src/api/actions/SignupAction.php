<?php

namespace photopro\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use photopro\api\providers\AuthnProviderInterface;
use photopro\core\application\ports\api\CredentialsDTO;

class SignupAction {
    public function __construct(
        private readonly AuthnProviderInterface $authnProvider
    )
    {}

    public function __invoke(Request $request, Response $response): Response {
        try {
            $data = $request->getParsedBody();
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';
            $role = (int)($data['role'] ?? 0);

            if (($email==='') OR ($password==='')){
                throw new \Exception("Email ou mot de passe non fourni");
            }
            
            $credentials = new CredentialsDTO($email, $password);
            $profile = $this->authnProvider->register($credentials, $role);

            $res = [
                'profile' => [
                    'id' => $profile->id,
                    'email' => $profile->email,
                    'role' => $profile->role
                ]
            ];

            $response->getBody()->write(json_encode($res, JSON_PRETTY_PRINT));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);


        }catch (\Exception $e){
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }
    }
}
