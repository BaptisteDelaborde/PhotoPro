<?php

namespace photopro\api\providers;

use photopro\core\application\ports\api\CredentialsDTO;
use photopro\core\application\ports\api\ProfileDTO;

interface AuthnProviderInterface
{
    public function register(
        CredentialsDTO $credentials,
        int $role,
        string $firstName,
        string $lastName,
        string $pseudo,
        ?string $phone
    ): ProfileDTO;

    public function signin(CredentialsDTO $credentials): array;
    public function refresh(string $token): array;
}