<?php

namespace photopro\api\providers;

use photopro\core\application\ports\api\AuthDTO;
use photopro\core\application\ports\api\CredentialsDTO;
use photopro\core\application\ports\api\ProfileDTO;

interface AuthnProviderInterface {
    public function register(CredentialsDTO $credentials, int $role): ProfileDTO;
    public function signin(CredentialsDTO $credentials): array;
    public function refresh(string $refreshToken): array;
    //public function getSignedInUser(string $token): ProfileDTO;
}
