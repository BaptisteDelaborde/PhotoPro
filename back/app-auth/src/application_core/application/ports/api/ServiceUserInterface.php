<?php
namespace photopro\core\application\ports\api;

interface ServiceUserInterface
{
    public function register(CredentialsDTO $credentials, int $role, string $firstName, string $lastName, string $pseudo, ?string $phone): ProfileDTO;
    public function byCredentials(CredentialsDTO $credentials): ?ProfileDTO;
}