<?php

namespace photopro\core\application\ports\spi\repositoryInterfaces;

use photopro\core\domain\entities\Galerie;
use photopro\core\domain\entities\Photo;

interface GalerieRepositoryInterface
{
    public function findById(string $id): ?Galerie;

    public function save(Galerie $galerie): void;

    public function findByPhotographerId(string $photographerId): array;

    public function savePhoto(Photo $photo): void;
    public function delete(string $id): void;
    public function getPhotosByGalerieId(string $galerieId): array;
    public function findByAccessCode(string $code): ?Galerie;
    public function findPublicGaleries(?string $photographerId = null): array;
    public function findByStatus(string $status): array;
    public function findPhotosByGalerieId(string $galerieId): array;

    public function getPhotoById(string $photoId): ?\photopro\core\domain\entities\Photo;
    public function deletePhoto(string $photoId): void;
    public function getAllPhotographes(): array;
}