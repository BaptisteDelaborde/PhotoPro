<?php
namespace photopro\core\application\ports\spi\repositoryInterfaces;

use photopro\core\application\ports\api\CredentialsDTO;
use photopro\core\domain\entities\User;
interface AuthRepositoryInterface
{
    public function findById(string $id): User;
    public function save(CredentialsDTO $dto, int $role, string $firstName, string $lastName, string $pseudo, ?string $phone): void;
    public function findByEmail(string $email): ?User;
    public function updatePhotographe(string $id, array $data): void;
    public function getPhotographeProfile(string $id): array;
}
