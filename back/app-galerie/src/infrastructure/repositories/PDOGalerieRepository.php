<?php

namespace photopro\infra\repositories;

use photopro\core\application\ports\spi\repositoryInterfaces\GalerieRepositoryInterface;
use photopro\core\domain\entities\Galerie;
use photopro\core\domain\entities\Photo;

class PDOGalerieRepository implements GalerieRepositoryInterface
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(string $id): ?Galerie
    {
        $sql = "SELECT * FROM galleries WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        // Instanciation claire grâce aux paramètres nommés (PHP 8)
        $galerie = new Galerie(
            id: $row['id'],
            photographer_id: $row['photographer_id'],
            title: $row['title'],
            layout: $row['layout'],
            is_public: (bool) $row['is_public'],
            created_at: $row['created_at'],
            description: $row['description'],
            access_code: $row['access_code'],
            access_url: $row['access_url'],
            client_name: $row['client_name'],
            client_email: $row['client_email']
        );

        // Astuce : si la galerie est publiée en base, on restaure son état
        if ($row['is_published']) {
            $reflection = new \ReflectionClass($galerie);
            $reflection->getProperty('is_published')->setValue($galerie, true);
            $reflection->getProperty('published_at')->setValue($galerie, $row['published_at']);
        }

        if ($row['cover_photo_id']) {
            $galerie->setCoverPhoto($row['cover_photo_id']);
        }

        return $galerie;
    }

    public function savePhoto(Photo $photo): void
    {
        $sql = "INSERT INTO photos (
                    id, photographer_id, title, file_name, mime_type, file_size, storage_url, uploaded_at
                ) VALUES (
                    :id, :photographer_id, :title, :file_name, :mime_type, :file_size, :storage_url, :uploaded_at
                )";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':id', $photo->getId());
        $stmt->bindValue(':photographer_id', $photo->getPhotographerId());
        $stmt->bindValue(':title', $photo->getTitle());
        $stmt->bindValue(':file_name', $photo->getFileName());
        $stmt->bindValue(':mime_type', $photo->getMimeType());
        $stmt->bindValue(':file_size', $photo->getFileSize());
        $stmt->bindValue(':storage_url', $photo->getStorageUrl());
        $stmt->bindValue(':uploaded_at', $photo->getUploadedAt());

        $stmt->execute();
    }

    public function save(Galerie $galerie): void
    {
        //On vérifie si la galerie existe déjà
        $sqlCheck = "SELECT id FROM galleries WHERE id = :id";
        $stmtCheck = $this->pdo->prepare($sqlCheck);

        //On stocke l'id dans une variable pour bindParam
        $id = $galerie->getId();
        $stmtCheck->bindParam(':id', $id);
        $stmtCheck->execute();

        $exists = $stmtCheck->fetchColumn();

        //Préparation de la requête (INSERT ou UPDATE)
        if ($exists) {
            $sql = "UPDATE galleries SET 
                        title = :title, description = :description, cover_photo_id = :cover_photo_id, 
                        is_public = :is_public, is_published = :is_published, layout = :layout, 
                        access_code = :access_code, access_url = :access_url, 
                        client_name = :client_name, client_email = :client_email, published_at = :published_at
                    WHERE id = :id";
        } else {
            $sql = "INSERT INTO galleries (
                        id, photographer_id, title, description, cover_photo_id, 
                        is_public, is_published, layout, access_code, access_url, 
                        client_name, client_email, created_at, published_at
                    ) VALUES (
                        :id, :photographer_id, :title, :description, :cover_photo_id, 
                        :is_public, :is_published, :layout, :access_code, :access_url, 
                        :client_name, :client_email, :created_at, :published_at
                    )";
        }

        $stmt = $this->pdo->prepare($sql);

        //Binding des valeurs
        $stmt->bindValue(':id', $galerie->getId(), \PDO::PARAM_STR);
        $stmt->bindValue(':photographer_id', $galerie->getPhotographerId(), \PDO::PARAM_STR);
        $stmt->bindValue(':title', $galerie->getTitle());
        $stmt->bindValue(':description', $galerie->getDescription());
        $stmt->bindValue(
            ':cover_photo_id',
            $galerie->getCoverPhotoId(),
            $galerie->getCoverPhotoId() ? \PDO::PARAM_STR : \PDO::PARAM_NULL
        );

        $stmt->bindValue(':is_public', $galerie->isPublic() ? 1 : 0, \PDO::PARAM_INT);
        $stmt->bindValue(':is_published', $galerie->isPublished() ? 1 : 0, \PDO::PARAM_INT);

        $stmt->bindValue(':layout', $galerie->getLayout());
        $stmt->bindValue(':access_code', $galerie->getAccessCode());
        $stmt->bindValue(':access_url', $galerie->getAccessUrl());
        $stmt->bindValue(':client_name', $galerie->getClientName());
        $stmt->bindValue(':client_email', $galerie->getClientEmail());
        $stmt->bindValue(':created_at', $galerie->getCreatedAt());
        $stmt->bindValue(':published_at', $galerie->getPublishedAt());

        $stmt->execute();
    }

    public function findByPhotographerId(string $photographerId): array
    {
        $sql = "SELECT * FROM galleries WHERE photographer_id = :photographer_id ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':photographer_id', $photographerId);
        $stmt->execute();

        $galeries = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $galerie = new Galerie(
                id: $row['id'],
                photographer_id: $row['photographer_id'],
                title: $row['title'],
                layout: $row['layout'],
                is_public: (bool) $row['is_public'],
                created_at: $row['created_at'],
                description: $row['description'],
                access_code: $row['access_code'],
                access_url: $row['access_url'],
                client_name: $row['client_name'],
                client_email: $row['client_email']
            );

            if ($row['is_published']) {
                $reflection = new \ReflectionClass($galerie);
                $reflection->getProperty('is_published')->setValue($galerie, true);
                $reflection->getProperty('published_at')->setValue($galerie, $row['published_at']);
            }

            if ($row['cover_photo_id']) {
                $galerie->setCoverPhoto($row['cover_photo_id']);
            }

            $galeries[] = $galerie;
        }

        return $galeries;
    }
}