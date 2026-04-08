<?php

namespace photopro\core\application\usecases;

use photopro\core\application\ports\api\GalerieDTO;
use photopro\core\application\ports\api\ServiceGalerieInterface;
use photopro\core\application\ports\spi\repositoryInterfaces\GalerieRepositoryInterface;
use photopro\core\application\ports\spi\NotificationPublisherInterface;
use photopro\core\domain\entities\Galerie;
use Ramsey\Uuid\Uuid;
use photopro\core\domain\entities\Photo;

class ServiceGalerie implements ServiceGalerieInterface
{
    private GalerieRepositoryInterface $galerieRepository;
    private NotificationPublisherInterface $publisher;

    public function __construct(
        GalerieRepositoryInterface $galerieRepository,
        NotificationPublisherInterface $publisher
    ) {
        $this->galerieRepository = $galerieRepository;
        $this->publisher = $publisher;
    }

    public function getGalerie(string $id): GalerieDTO
    {
        $galerie = $this->galerieRepository->findById($id);

        if (!$galerie) {
            throw new \Exception("La galerie demandée n'existe pas.");
        }

        return $this->toDTO($galerie);
    }

    public function createGalerie(
        string $photographer_id,
        string $title,
        string $layout,
        bool $is_public,
        ?string $description = null,
        ?string $client_name = null,
        ?string $client_email = null
    ): GalerieDTO {
        $id = Uuid::uuid4()->toString();
        $createdAt = (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s');
        $accessCode = null;
        $accessUrl = null;

        if (!$is_public) {
            if (empty($client_email)) {
                throw new \InvalidArgumentException("Une galerie privée doit avoir un email client.");
            }
            $accessCode = substr(bin2hex(random_bytes(4)), 0, 8);
            $accessUrl = "/p/" . $accessCode;
        }
        $galerie = new Galerie(
            id: $id,
            photographer_id: $photographer_id,
            title: $title,
            layout: $layout,
            is_public: $is_public,
            created_at: $createdAt,
            description: $description,
            access_code: $accessCode,
            access_url: $accessUrl,
            client_name: $client_name,
            client_email: $client_email
        );
        $this->galerieRepository->save($galerie);
        return $this->toDTO($galerie);
    }
    private function toDTO(Galerie $galerie): GalerieDTO
    {
        $dto = new GalerieDTO(
            id: $galerie->getId(),
            photographer_id: $galerie->getPhotographerId(),
            title: $galerie->getTitle(),
            layout: $galerie->getLayout(),
            is_public: $galerie->isPublic(),
            is_published: $galerie->isPublished(),
            created_at: $galerie->getCreatedAt(),
            description: $galerie->getDescription(),
            cover_photo_id: $galerie->getCoverPhotoId(),
            access_code: $galerie->getAccessCode(),
            access_url: $galerie->getAccessUrl(),
            client_name: $galerie->getClientName(),
            client_email: $galerie->getClientEmail(),
            published_at: $galerie->getPublishedAt()
        );

        if ($galerie->getCoverPhotoId()) {
            try {
                $photo = $this->galerieRepository->getPhotoById($galerie->getCoverPhotoId());
                if ($photo) {
                    $link = $photo->getStorageUrl();
                    
                    if (str_contains($link, 'http://localhost:8333/photopro-galeries/http')) {
                        $link = str_replace('http://localhost:8333/photopro-galeries/', '', $link);
                    }
                    
                    $s3Endpoint = (string) (getenv('S3_EXTERNAL_ENDPOINT') ?: 'http://localhost:8333');
                    $s3Bucket   = (string) (getenv('S3_BUCKET') ?: 'photopro-galeries');
                    
                    $dto->cover_url = str_starts_with($link, 'http')
                        ? $link
                        : $s3Endpoint . '/' . $s3Bucket . '/' . $link;
                }
            } catch (\Exception $e) {
                $dto->cover_url = null;
            }
        }

        return $dto;
    }

    public function ajouterPhoto(string $photographer_id, string $galerie_id, string $file_name, string $mime_type, float $file_size, string $s3_key): Photo
    {
        $photoId = Uuid::uuid4()->toString();
        $uploadedAt = date('Y-m-d H:i:s');

        $photo = new Photo(
            $photoId,
            $photographer_id,
            $galerie_id,
            $file_name,
            $mime_type,
            $file_size,
            $s3_key,
            $uploadedAt
        );

        $this->galerieRepository->savePhoto($photo);

        return $photo;
    }

    public function getPhotos(string $galerie_id): array
    {
        $photos = $this->galerieRepository->getPhotosByGalerieId($galerie_id);

        $result = [];
        
        // On récupère les variables d'environnement de manière sécurisée
        $s3Endpoint = (string) (getenv('S3_EXTERNAL_ENDPOINT') ?: 'http://localhost:8333');
        $s3Bucket   = (string) (getenv('S3_BUCKET') ?: 'photopro-galeries');

        foreach ($photos as $photo) {
            $result[] = [
                'id' => $photo->getId(),
                'title' => $photo->getTitle(),
                'file_name' => $photo->getFileName(),
                'mime_type' => $photo->getMimeType(),
                'file_size' => $photo->getFileSize(),
                's3_key' => $photo->getStorageUrl(),
                'url' => str_starts_with($photo->getStorageUrl(), 'http')
                    ? $photo->getStorageUrl()
                    : $s3Endpoint . '/' . $s3Bucket . '/' . $photo->getStorageUrl(),
                'uploaded_at' => $photo->getUploadedAt()
            ];
        }

        return $result;
    }
    public function getGaleriesByPhotographer(string $photographerId): array
    {
        $galeries = $this->galerieRepository->findByPhotographerId($photographerId);

        $dtos = [];
        foreach ($galeries as $galerie) {
            $dtos[] = $this->toDTO($galerie);
        }

        return $dtos;
    }

    public function deleteGalerie(string $id): void
    {
        $this->galerieRepository->delete($id);
    }

    public function updateStatus(string $id, array $data): GalerieDTO
    {
        $galerie = $this->galerieRepository->findById($id);

        if (!$galerie) {
            throw new \Exception("La galerie demandée n'existe pas.", 404);
        }

        if (isset($data['is_public'])) {
            $galerie->setIsPublic((bool) $data['is_public']);
        }

        if (isset($data['is_published'])) {
            $isPublished = (bool) $data['is_published'];

            if ($isPublished && !$galerie->isPublished()) {
                $photos = $this->galerieRepository->getPhotosByGalerieId($id);
                if (count($photos) === 0) {
                    throw new \InvalidArgumentException("La galerie doit contenir au moins une photo pour être publiée.");
                }
                $galerie->publier();
            } elseif (!$isPublished && $galerie->isPublished()) {
                $galerie->depublier();
            }
        }

        $this->galerieRepository->save($galerie);

        $dto = $this->toDTO($galerie);

        // Notification uniquement pour les galeries privées avec un client
        if (!$galerie->isPublic() && $galerie->getClientEmail()) {
            $frontendUrl = getenv('FRONTEND_URL') ?: 'http://localhost:3001';
            $payload = [
                'galerie' => [
                    'id'          => $dto->id,
                    'titre'       => $dto->title,
                    'description' => $dto->description,
                    'access_url'  => $dto->access_url,
                    'access_code' => $dto->access_code,
                    'url'         => $frontendUrl . $dto->access_url,
                ],
                'destinataires' => [
                    ['type' => 'client', 'email' => $galerie->getClientEmail()]
                ]
            ];

            if (isset($data['is_published'])) {
                $event = (bool) $data['is_published'] ? 'PUBLISHED' : 'UNPUBLISHED';
                $this->publisher->publish($event, $payload);
            }
        }

        return $dto;
    }

    public function updateGalerie(string $id, array $data): GalerieDTO
    {
        $galerie = $this->galerieRepository->findById($id);

        if (!$galerie) {
            throw new \Exception("La galerie demandée n'existe pas.", 404);
        }

        if (isset($data['title'])) {
            $galerie->setTitle($data['title']);
        }
        if (array_key_exists('description', $data)) {
            $galerie->setDescription($data['description']);
        }
        if (isset($data['layout'])) {
            $galerie->setLayout($data['layout']);
        }

        $this->galerieRepository->save($galerie);

        $dto = $this->toDTO($galerie);

        // Notification si galerie privée, publiée, avec un client
        if (!$galerie->isPublic() && $galerie->isPublished() && $galerie->getClientEmail()) {
            $frontendUrl = getenv('FRONTEND_URL') ?: 'http://localhost:3001';
            $payload = [
                'galerie' => [
                    'id'          => $dto->id,
                    'titre'       => $dto->title,
                    'description' => $dto->description,
                    'access_url'  => $dto->access_url,
                    'access_code' => $dto->access_code,
                    'url'         => $frontendUrl . $dto->access_url,
                ],
                'destinataires' => [
                    ['type' => 'client', 'email' => $galerie->getClientEmail()]
                ]
            ];
            $this->publisher->publish('MODIFIED', $payload);
        }

        return $dto;
    }

    public function getPublicGaleries(?string $photographerId = null): array {
        $galeries = $this->galerieRepository->findPublicGaleries($photographerId);

        return array_map(fn($g) => $this->toDTO($g), $galeries);
    }

    public function getGalerieByCode(string $code): GalerieDTO
    {
        $galerie = $this->galerieRepository->findByAccessCode($code);

        if (!$galerie) {
            throw new \Exception("Galerie introuvable ou code invalide.", 404);
        }

        if (!$galerie->isPublished()) {
            throw new \Exception("Cette galerie n'est pas encore disponible à la consultation.", 403);
        }

        return $this->toDTO($galerie);
    }

    public function getPhotosByGalerie(string $galerieId): array {
        $photos = $this->galerieRepository->findPhotosByGalerieId($galerieId);

        return $photos;
    }

    public function deletePhoto(string $photoId): string 
    {
        $photo = $this->galerieRepository->getPhotoById($photoId);
        
        if (!$photo) {
            throw new \Exception("La photo demandée n'existe pas.");
        }

        $s3Key = $photo->getStorageUrl();

        $this->galerieRepository->deletePhoto($photoId);

        return $s3Key;
    }

    public function addCommentaire(string $photoId, string $content, ?string $authorName): array
    {
        $photo = $this->galerieRepository->getPhotoById($photoId);
        if (!$photo) {
            throw new \Exception("Photo introuvable.", 404);
        }

        return $this->galerieRepository->saveCommentaire($photoId, $content, $authorName);
    }

    public function getCommentaires(string $photoId): array
    {
        return $this->galerieRepository->getCommentairesByPhotoId($photoId);
    }

    public function getPhotographes(): array
    {
        return $this->galerieRepository->getAllPhotographes();
    }
}