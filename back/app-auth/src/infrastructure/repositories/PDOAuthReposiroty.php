<?php
namespace photopro\infra\repositories;

use photopro\core\application\ports\api\CredentialsDTO;
use photopro\core\application\ports\spi\repositoryInterfaces\AuthRepositoryInterface;
use photopro\core\domain\entities\User;
use Ramsey\Uuid\Uuid;

class PDOAuthReposiroty implements AuthRepositoryInterface {
    private \PDO $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findById(string $id): User {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return new User(
            id: $row['id'],
            email: $row['email'],
            password: $row['password'],
            role: $row['role']
        );
    }

    public function save(CredentialsDTO $dto, int $role):void {
        $id = Uuid::uuid4()->toString();
        $passwordhash = password_hash($dto->password, PASSWORD_BCRYPT);

        $this->pdo->beginTransaction();
        try {
            $sql = "INSERT INTO users (id, email, password, role) VALUES (:id, :email, :password, :role)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':email', $dto->email);
            $stmt->bindParam(':password', $passwordhash);
            $stmt->bindParam(':role', $role);
            $stmt->execute();

            //On crée le photographe correspondant
            $photoId = Uuid::uuid4()->toString();
            $emailParts = explode('@', $dto->email);
            $pseudo = $emailParts[0] . '_' . substr(md5(uniqid()), 0, 4); // Ensures unique pseudo
            $firstName = $emailParts[0];
            $lastName = 'Photographe';

            $sqlPhoto = "INSERT INTO photographes (id, auth_user_id, pseudo, first_name, last_name, email) 
                         VALUES (:id, :auth_user_id, :pseudo, :first_name, :last_name, :email)";
            $stmtPhoto = $this->pdo->prepare($sqlPhoto);
            $stmtPhoto->bindParam(':id', $photoId);
            $stmtPhoto->bindParam(':auth_user_id', $id);
            $stmtPhoto->bindParam(':pseudo', $pseudo);
            $stmtPhoto->bindParam(':first_name', $firstName);
            $stmtPhoto->bindParam(':last_name', $lastName);
            $stmtPhoto->bindParam(':email', $dto->email);
            $stmtPhoto->execute();

            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function findByEmail(string $email): ?User {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new User(
            id: $row['id'],
            email: $row['email'],
            password: $row['password'],
            role: $row['role']
        );
    }
}
