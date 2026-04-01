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

    public function ajouterPhoto(string $photographer_id, string $file_name, string $mime_type, float $file_size, string $s3_key): \photopro\core\domain\entities\Photo;

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
    public function updateStatus(string $id, array $data): GalerieDTO;
}