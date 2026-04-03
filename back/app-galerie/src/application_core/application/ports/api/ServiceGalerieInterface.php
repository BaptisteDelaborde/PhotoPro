<?php

namespace photopro\core\application\ports\api;

interface ServiceGalerieInterface
{
    /**
     * Récupère une galerie par son identifiant et la retourne sous forme de DTO.
     * @param string $id L'UUID de la galerie
     * @return GalerieDTO
     * @throws \Exception Si la galerie n'est pas trouvée
     */
    public function getGalerie(string $id): GalerieDTO;

    public function ajouterPhoto(string $photographer_id, string $galerie_id, string $file_name, string $mime_type, float $file_size, string $s3_key): \photopro\core\domain\entities\Photo;

    /**
     * Crée une nouvelle galerie (publique ou privée)
     * @param string $photographer_id
     * @param string $title
     * @param string $layout
     * @param bool $is_public
     * @param string|null $description
     * @param string|null $client_name
     * @param string|null $client_email
     * @return GalerieDTO
     * @throws \InvalidArgumentException
     */
    public function createGalerie(
        string $photographer_id,
        string $title,
        string $layout,
        bool $is_public,
        ?string $description = null,
        ?string $client_name = null,
        ?string $client_email = null
    ): GalerieDTO;

    /**
     * Récupère toutes les galeries d'un photographe
     * @param string $photographerId
     * @return GalerieDTO[] Tableau de DTOs de galeries
     */
    public function getGaleriesByPhotographer(string $photographerId): array;
    public function getPhotos(string $galerie_id): array;
    /**
     * Supprime une galerie par son identifiant
     * @param string $id L'UUID de la galerie
     * @return void
     */
    public function deleteGalerie(string $id): void;

    /**
     * Met à jour le statut (visibilité/publication) d'une galerie
     * @param string $id L'UUID de la galerie
     * @param array $data Les données à mettre à jour (ex: ['is_published' => true])
     * @return GalerieDTO La galerie mise à jour
     * @throws \Exception Si la galerie n'est pas trouvée
     */

    /**
     * Met à jour le statut (visibilité/publication) d'une galerie
     * @param string $id L'UUID de la galerie
     * @param array $data Les données à mettre à jour (ex: ['is_published' => true])
     * @return GalerieDTO La galerie mise à jour
     * @throws \Exception Si la galerie n'est pas trouvée
     */
    public function updateStatus(string $id, array $data): GalerieDTO;

    /**
     * Récupère une galerie privée via son code d'accès (front-office)
     * @param string $code Le code d'accès unique de la galerie
     * @return GalerieDTO La galerie correspondante
     * @throws \Exception Si la galerie est introuvable ou si elle n'est pas publiée
     */
    public function getGalerieByCode(string $code): GalerieDTO;

    /**
     * Met à jour les informations d'une galerie (titre, description, client, etc.)
     * @param string $id
     * @param array $data
     * @return GalerieDTO
     */
    public function updateGalerie(string $id, array $data): GalerieDTO;

    /**
     * Récupère toutes les galeries publiques
     * @return GalerieDTO[] Tableau de DTOs de galeries publiques
     */
    public function getPublicGaleries(): array;

    /**
     * Récupère les photos d'une galerie par son identifiant
     * @param string $galerieId
     * @return array
     */
    public function getPhotosByGalerie(string $galerieId): array;
}