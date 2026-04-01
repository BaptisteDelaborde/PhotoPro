<?php

namespace photopro\core\application\usecases;

use photopro\core\application\ports\api\GalerieDTO;
use photopro\core\application\ports\api\ServiceGalerieInterface;
use photopro\core\application\ports\spi\repositoryInterfaces\GalerieRepositoryInterface;
use photopro\core\domain\entities\Galerie;
use Ramsey\Uuid\Uuid;
use photopro\core\domain\entities\Photo;

class ServiceGalerie implements ServiceGalerieInterface
{
    private GalerieRepositoryInterface $galerieRepository;

    public function __construct(GalerieRepositoryInterface $galerieRepository)
    {
        $this->galerieRepository = $galerieRepository;
    }

    public function getGalerie(string $id): GalerieDTO
    {
        $galerie = $this->galerieRepository->findById($id);
        
        if (!$galerie) {
            // Idéalement, tu devrais créer une exception personnalisée comme NotFoundException
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
            $accessUrl = "/galeries/privee/" . $id;
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
        return new GalerieDTO(
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
    }

    public function ajouterPhoto(string $photographer_id, string $file_name, string $mime_type, float $file_size, string $s3_key): Photo {
        $photoId = Uuid::uuid4()->toString();
        $uploadedAt = date('Y-m-d H:i:s'); 
        
        $photo = new Photo(
            $photoId,
            $photographer_id,
            $file_name,
            $mime_type,
            $file_size,
            $s3_key, 
            $uploadedAt
        );
        
        $this->galerieRepository->savePhoto($photo);
        
        return $photo;
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
            $galerie->setIsPublic((bool)$data['is_public']);
        }

        if (isset($data['is_published'])) {
            $isPublished = (bool)$data['is_published'];
            
            if ($isPublished && !$galerie->isPublished()) {
                $galerie->publier();
            } elseif (!$isPublished && $galerie->isPublished()) {
                $galerie->depublier();
            }
        }

        $this->galerieRepository->save($galerie);
        
        return $this->toDTO($galerie);
    }
}