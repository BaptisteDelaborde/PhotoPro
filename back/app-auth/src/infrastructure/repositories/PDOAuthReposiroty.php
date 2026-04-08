<?php

namespace photopro\infra\repositories;

use photopro\core\application\ports\api\CredentialsDTO;
use photopro\core\application\ports\spi\repositoryInterfaces\AuthRepositoryInterface;
use photopro\core\domain\entities\User;
use Ramsey\Uuid\Uuid;

class PDOAuthReposiroty implements AuthRepositoryInterface
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(string $id): User
    {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return new User(
            id: $row['id'],
            email: $row['email'],
            password: $row['password'],
            role: $row['role']
        );
    }

    // AJOUT DES NOUVEAUX PARAMÈTRES ICI POUR NE PLUS FAIRE DE "MERDE"
    public function save(CredentialsDTO $dto, int $role, string $firstName, string $lastName, string $pseudo, ?string $phone): void
    {
        $id = Uuid::uuid4()->toString();
        $passwordhash = password_hash($dto->password, PASSWORD_BCRYPT);

        $this->pdo->beginTransaction();
        try {
            // 1. Insertion dans la table users
            $sql = "INSERT INTO users (id, email, password, role) VALUES (:id, :email, :password, :role)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':email' => $dto->email,
                ':password' => $passwordhash,
                ':role' => $role
            ]);

            // 2. Insertion dans la table photographes avec les VRAIES données
            $photoId = Uuid::uuid4()->toString();

            $sqlPhoto = "INSERT INTO photographes (id, auth_user_id, pseudo, first_name, last_name, email, phone) 
                         VALUES (:id, :auth_user_id, :pseudo, :first_name, :last_name, :email, :phone)";
            $stmtPhoto = $this->pdo->prepare($sqlPhoto);
            $stmtPhoto->execute([
                ':id' => $photoId,
                ':auth_user_id' => $id,
                ':pseudo' => $pseudo,       // Utilise le pseudo du front
                ':first_name' => $firstName, // Utilise le prénom du front
                ':last_name' => $lastName,   // Utilise le nom du front
                ':email' => $dto->email,
                ':phone' => $phone           // Utilise le téléphone du front (ou null)
            ]);

            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function findByEmail(string $email): ?User
    {
        $sql = "SELECT u.*, p.id as photographer_id, p.pseudo, p.first_name, p.last_name, p.phone 
            FROM users u 
            LEFT JOIN photographes p ON u.id = p.auth_user_id 
            WHERE u.email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row)
            return null;

        $user = new User(
            id: $row['id'],
            email: $row['email'],
            password: $row['password'],
            role: (int) $row['role']
        );

        $user->setProfileData([
            'photographer_id' => $row['photographer_id'],
            'pseudo' => $row['pseudo'],
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'phone' => $row['phone']
        ]);

        return $user;
    }

    public function updatePhotographe(string $id, array $data): void
    {
        $fields = [];
        $params = [':id' => $id];
        if (isset($data['description'])) {
            $fields[] = 'description = :description';
            $params[':description'] = $data['description'];
        }
        if (isset($data['profile_image_url'])) {
            $fields[] = 'profile_image_url = :profile_image_url';
            $params[':profile_image_url'] = $data['profile_image_url'];
        }

        if (empty($fields)) {
            return;
        }

        $sql = "UPDATE photographes SET " . implode(', ', $fields) . " WHERE auth_user_id = :id OR id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }
}