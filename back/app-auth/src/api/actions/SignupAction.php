<?php

namespace photopro\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use photopro\api\providers\AuthnProviderInterface;
use photopro\core\application\ports\api\CredentialsDTO;

class SignupAction
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
            $role = (int) ($data['role'] ?? 0);

            $firstName = $data['first_name'] ?? '';
            $lastName = $data['last_name'] ?? '';
            $pseudo = $data['pseudo'] ?? '';
            $phone = $data['phone'] ?? null;

            if (($email === '') OR ($password === '')) {
                throw new \Exception("Email ou mot de passe non fourni");
            }
            if (($firstName === '') OR ($lastName === '') OR ($pseudo === '')) {
                throw new \Exception("Prénom, nom ou pseudo non fourni");
            }

            $credentials = new CredentialsDTO($email, $password);

            $profile = $this->authnProvider->register($credentials, $role, $firstName, $lastName, $pseudo, $phone);

            $res = [
                'profile' => [
                    'id' => $profile->id,
                    'email' => $profile->email,
                    'role' => $profile->role,
                    'pseudo' => $profile->pseudo,     // Utilise l'objet ProfileDTO
                    'first_name' => $profile->firstName,  // Ajouté
                    'last_name' => $profile->lastName,   // Ajouté
                    'phone' => $profile->phone       // Ajouté
                ]
            ];

            $response->getBody()->write(json_encode($res, JSON_PRETTY_PRINT));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);


        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }
    }
}