<?php

namespace photopro\core\application\usecases;
use photopro\core\application\ports\api\CredentialsDTO;
use photopro\core\application\ports\api\ProfileDTO;
use photopro\core\application\ports\api\ServiceUserInterface;
use photopro\core\application\ports\spi\repositoryInterfaces\AuthRepositoryInterface;

class ServiceUser implements ServiceUserInterface
{

    private AuthRepositoryInterface $userRepository;

    public function __construct(AuthRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(CredentialsDTO $credentials, int $role, string $firstName, string $lastName, string $pseudo, ?string $phone): ProfileDTO
    {
        // On enregistre
        $this->userRepository->save($credentials, $role, $firstName, $lastName, $pseudo, $phone);

        // On récupère l'utilisateur complet pour renvoyer le ProfileDTO
        $user = $this->userRepository->findByEmail($credentials->email);

        return new ProfileDTO(
            $user->getId(),
            $user->getEmail(),
            $user->getRole(),
            $pseudo,
            $firstName,
            $lastName,
            $phone
        );
    }

    public function byCredentials(CredentialsDTO $credentials): ?ProfileDTO
    {
        $user = $this->userRepository->findByEmail($credentials->email);
        if ($user === null) {
            throw new \Exception("Email erroné");
        }

        if (!password_verify($credentials->password, $user->getPassword())) {
            throw new \Exception("Mot de passe erroné");
        }
        $data = $user->getProfileData();

        return new ProfileDTO(
            $user->getId(),
            $user->getEmail(),
            $user->getRole(),
            $data['pseudo'] ?? null,
            $data['first_name'] ?? null,
            $data['last_name'] ?? null,
            $data['phone'] ?? null
        );
    }
    public function updateProfile(string $id, array $data): void
    {
        $this->userRepository->updatePhotographe($id, $data);
    }
}