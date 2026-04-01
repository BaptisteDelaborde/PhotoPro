<?php

namespace photopro\core\application\ports\spi\repositoryInterfaces;

use photopro\core\domain\entities\Galerie;

interface GalerieRepositoryInterface
{
    /**
     * Sauvegarde une nouvelle galerie ou met à jour une galerie existante.
     */
    public function save(Galerie $galerie): void;

    /**
     * Récupère une galerie par son identifiant unique.
     * Retourne null si la galerie n'existe pas.
     */
    public function findById(string $id): ?Galerie;

    /**
     * Récupère toutes les galeries d'un photographe spécifique.
     * Retourne un tableau d'objets Galerie.
     */
    public function findByPhotographerId(string $photographer_id): array;
}