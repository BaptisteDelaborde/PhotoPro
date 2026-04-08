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

        return $this->hydrateGalerie($row);
    }

    public function findByAccessCode(string $code): ?Galerie
    {
        $sql = "SELECT * FROM galleries WHERE access_code = :code";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':code', $code);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return $this->hydrateGalerie($row);
    }

    public function getPhotoById(string $photoId): ?Photo 
    {
        $sql = "SELECT * FROM photos WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $photoId);
        $stmt->execute();
        
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return new Photo(
            $row['id'], 
            $row['photographer_id'], 
            $row['galerie_id'] ?? null,
            $row['file_name'],
            $row['mime_type'], 
            (float) $row['file_size'], 
            $row['storage_url'] ?? $row['s3_key'] ?? '',
            $row['uploaded_at'], 
            $row['title'] ?? null
        );
    }

    public function deletePhoto(string $photoId): void 
    {
        $sql = "DELETE FROM photos WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $photoId);
        $stmt->execute();
    }

    public function savePhoto(Photo $photo): void
    {
        $sql = "INSERT INTO photos (
                    id, photographer_id, galerie_id, title, file_name, mime_type, file_size, storage_url, uploaded_at
                ) VALUES (
                    :id, :photographer_id, :galerie_id, :title, :file_name, :mime_type, :file_size, :storage_url, :uploaded_at
                )";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':id', $photo->getId());
        $stmt->bindValue(':photographer_id', $photo->getPhotographerId());
        $stmt->bindValue(':galerie_id', $photo->getGalerieId());
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
        $sqlCheck = "SELECT id FROM galleries WHERE id = :id";
        $stmtCheck = $this->pdo->prepare($sqlCheck);

        $id = $galerie->getId();
        $stmtCheck->bindParam(':id', $id);
        $stmtCheck->execute();

        $exists = $stmtCheck->fetchColumn();

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

        $stmt->bindValue(':id', $galerie->getId(), \PDO::PARAM_STR);

        if (!$exists) {
            $stmt->bindValue(':photographer_id', $galerie->getPhotographerId(), \PDO::PARAM_STR);
            $stmt->bindValue(':created_at', $galerie->getCreatedAt());
        }

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
        $stmt->bindValue(':published_at', $galerie->getPublishedAt());

        $stmt->execute();
    }

    public function delete(string $id): void
    {
        $sql = "DELETE FROM galleries WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    public function getPhotosByGalerieId(string $galerieId): array
    {
        $sql = "SELECT p.* FROM photos p
                INNER JOIN gallery_photos gp ON p.id = gp.photo_id
                WHERE gp.gallery_id = :galerie_id
                ORDER BY gp.added_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':galerie_id', $galerieId);
        $stmt->execute();

        $photos = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $photos[] = new Photo(
                $row['id'],
                $row['photographer_id'],
                $galerieId,
                $row['file_name'],
                $row['mime_type'],
                (float) $row['file_size'],
                $row['storage_url'] ?? $row['s3_key'] ?? '',
                $row['uploaded_at'],
                $row['title'] ?? null
            );
        }
        return $photos;
    }

    public function findPublicGaleries(?string $photographerId = null): array {
        $sql = "SELECT * FROM galleries WHERE is_public = true AND is_published = true";

        if ($photographerId) {
            $sql .= " AND photographer_id = :photographer_id";
        }

        $sql .= " ORDER BY published_at DESC";

        $stmt = $this->pdo->prepare($sql);

        if ($photographerId) {
            $stmt->bindParam(':photographer_id', $photographerId);
        }

        $stmt->execute();

        $galeries = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $galeries[] = $this->hydrateGalerie($row);
        }

        return $galeries;
    }

    public function findByPhotographerId(string $photographerId): array
    {
        $sql = "SELECT * FROM galleries WHERE photographer_id = :photographer_id ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':photographer_id', $photographerId);
        $stmt->execute();

        $galeries = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $galeries[] = $this->hydrateGalerie($row);
        }

        return $galeries;
    }

    private function hydrateGalerie(array $row): Galerie
    {
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

        return $galerie;
    }

    public function findByStatus(string $status): array {
        $sql = "SELECT * FROM galeries WHERE status = :status";
        $stmt = $this->pdo->prepare($sql);

        $stmt->execute(['status' => $status]);

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $rows;
    }

    public function findPhotosByGalerieId(string $galerieId): array {
        $sql = "SELECT * FROM photos WHERE galerie_id = :galerie_id";
        $stmt = $this->pdo->prepare($sql);

        $stmt->execute(['galerie_id' => $galerieId]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function saveCommentaire(string $photoId, string $content, ?string $authorName): array
    {
        $id = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $sql = "INSERT INTO comments (id, photo_id, author_name, content) VALUES (:id, :photo_id, :author_name, :content)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id'          => $id,
            'photo_id'    => $photoId,
            'author_name' => $authorName,
            'content'     => $content,
        ]);

        return [
            'id'          => $id,
            'photo_id'    => $photoId,
            'author_name' => $authorName,
            'content'     => $content,
            'created_at'  => date('Y-m-d H:i:s'),
        ];
    }

    public function getCommentairesByPhotoId(string $photoId): array
    {
        $sql = "SELECT * FROM comments WHERE photo_id = :photo_id ORDER BY created_at ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['photo_id' => $photoId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findPublicGaleriesP(?string $photographerId = null): array {
        $sql = "SELECT * FROM galleries WHERE is_public = true AND is_published = true";

        if ($photographerId) {
            $sql .= " AND photographer_id = :photographer_id";
        }

        // "Par date de publication" (le plus récent en premier)
        $sql .= " ORDER BY published_at DESC";

        $stmt = $this->pdo->prepare($sql);

        if ($photographerId) {
            $stmt->bindParam(':photographer_id', $photographerId);
        }

        $stmt->execute();

        $galeries = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $galeries[] = $this->hydrateGalerie($row);
        }

        return $galeries;
    }

    public function getAllPhotographes(): array {
        $sql = "SELECT id, first_name, last_name, pseudo FROM photographes ORDER BY first_name ASC";

        $host = getenv('AUTH_DB_HOST') ?: 'auth.db';
        $port = getenv('AUTH_DB_PORT') ?: 5432;
        $db   = getenv('AUTH_POSTGRES_DB') ?: 'photopro_auth';
        $user = getenv('AUTH_POSTGRES_USER') ?: 'photopro';
        $pass = getenv('AUTH_POSTGRES_PASSWORD') ?: 'photopro';

        $dsn = "pgsql:host=$host;port=$port;dbname=$db";

        try {
            $authPdo = new \PDO($dsn, $user, $pass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]);
            $stmt = $authPdo->query($sql);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('[getAllPhotographes] Connexion auth.db échouée : ' . $e->getMessage());
            return [];
        }
    }
}