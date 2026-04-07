<?php
namespace photopro\api\providers;

use photopro\core\application\ports\api\AuthDTO;
use photopro\core\application\ports\api\CredentialsDTO;
use photopro\core\application\ports\api\ProfileDTO;
use photopro\core\application\ports\api\ServiceUserInterface;

class JWTAuthnProvider implements AuthnProviderInterface
{

    private ServiceUserInterface $serviceUser;
    private JWTManager $JWTManager;

    public function __construct(JWTManager $jwtManager, ServiceUserInterface $serviceUser)
    {
        $this->JWTManager = $jwtManager;
        $this->serviceUser = $serviceUser;
    }

    /**
     * CORRECTION : La signature doit correspondre à AuthnProviderInterface
     */
    public function register(
        CredentialsDTO $credentials,
        int $role,
        string $firstName,
        string $lastName,
        string $pseudo,
        ?string $phone
    ): ProfileDTO {
        return $this->serviceUser->register($credentials, $role, $firstName, $lastName, $pseudo, $phone);
    }

    public function signin(CredentialsDTO $credentials): array
    {
        $user = $this->serviceUser->byCredentials($credentials);

        $payload = [
            'iss' => 'http://photopro',
            'iat' => time(),
            'exp' => time() + 3600,
            'sub' => $user->id,
            'data' => [
                'role' => $user->role,
                'user' => $user->email
            ]
        ];

        $accessToken = $this->JWTManager->createAccesToken($payload);
        $refreshToken = $this->JWTManager->createRefreshToken($payload);

        return [
            new AuthDTO($accessToken, $refreshToken),
            $user
        ];
    }

    public function refresh(string $refreshToken): array
    {
        $decoded = $this->JWTManager->decodeToken($refreshToken);
        if ($decoded['type'] !== 'refresh') {
            throw new \Exception("Invalid token type");
        }

        $payload = [
            'iss' => 'http://photopro',
            'iat' => time(),
            'exp' => time() + 3600,
            'sub' => $decoded['sub'],
            'data' => (array) $decoded['data']
        ];

        $newAccessToken = $this->JWTManager->createAccesToken($payload);
        $newRefreshToken = $this->JWTManager->createRefreshToken($payload);

        return [
            new AuthDTO($newAccessToken, $newRefreshToken),
            new ProfileDTO(
                $decoded['sub'],
                $decoded['data']->user ?? $decoded['data']['user'],
                $decoded['data']->role ?? $decoded['data']['role']
            )
        ];
    }
}