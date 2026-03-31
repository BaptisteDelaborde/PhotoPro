<?php
namespace photopro\api\providers;

use Firebase\JWT\JWT;
use photopro\core\application\ports\api\AuthDTO;
use photopro\core\application\ports\api\CredentialsDTO;
use photopro\core\application\ports\api\ProfileDTO;
use photopro\core\application\ports\api\ServiceUserInterface;

class JWTAuthnProvider implements AuthnProviderInterface{

    private ServiceUserInterface $serviceUser;
    private JWTManager $JWTManager;

    public function __construct(JWTManager $jwtManager, ServiceUserInterface $serviceUser){
        $this->JWTManager = $jwtManager;
        $this->serviceUser = $serviceUser;
    }

    public function signin(CredentialsDTO $credentials): array
    {
        $user = $this->serviceUser->byCredentials($credentials);
        $payload = [
            'iss' => 'http://photopro',
            'iat' => time(),
            'exp' => time()+3600,
            'sub' => $user->id,
            'data' => [
                'role' => $user->role,
                'user' => $user->email
            ]
        ];
        $accessToken  = $this->JWTManager->createAccesToken($payload);
        $refreshToken = $this->JWTManager->createRefreshToken($payload);

        return [new AuthDTO($accessToken, $refreshToken), new ProfileDTO($user->id,$user->email,$user->role)];
    }
}
