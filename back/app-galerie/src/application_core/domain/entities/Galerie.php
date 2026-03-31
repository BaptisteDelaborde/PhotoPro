<?php

namespace photopro\galerie\domain\entities;

class Galerie
{
    private string $id;
    private string $photographer_id;
    private string $title;
    private ?string $description;
    private ?string $cover_photo_id;
    private bool $is_public;
    private bool $is_published;
    private string $layout;
    private ?string $access_code;
    private ?string $access_url;
    private ?string $client_name;
    private ?string $client_email;
    private string $created_at;
    private ?string $published_at;

    public function __construct(
        string $id,
        string $photographer_id,
        string $title,
        string $layout,
        bool $is_public,
        string $created_at,
        ?string $description = null,
        ?string $access_code = null,
        ?string $access_url = null,
        ?string $client_name = null,
        ?string $client_email = null
    ) {
        $this->id = $id;
        $this->photographer_id = $photographer_id;
        $this->title = $title;
        $this->layout = $layout;
        $this->is_public = $is_public;
        $this->created_at = $created_at;
        
        // Valeurs par défaut à la création
        $this->is_published = false;
        $this->published_at = null;
        $this->cover_photo_id = null;

        // Champs optionnels
        $this->description = $description;
        $this->access_code = $access_code;
        $this->access_url = $access_url;
        $this->client_name = $client_name;
        $this->client_email = $client_email;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPhotographerId(): string
    {
        return $this->photographer_id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCoverPhotoId(): ?string
    {
        return $this->cover_photo_id;
    }

    public function isPublic(): bool
    {
        return $this->is_public;
    }

    public function isPublished(): bool
    {
        return $this->is_published;
    }

    public function getLayout(): string
    {
        return $this->layout;
    }

    public function getAccessCode(): ?string
    {
        return $this->access_code;
    }

    public function getAccessUrl(): ?string
    {
        return $this->access_url;
    }

    public function getClientName(): ?string
    {
        return $this->client_name;
    }

    public function getClientEmail(): ?string
    {
        return $this->client_email;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function getPublishedAt(): ?string
    {
        return $this->published_at;
    }

    public function publier(): void
    {
        if ($this->is_published) {
            throw new \RuntimeException('La galerie est déjà publiée.');
        }
        
        $this->is_published = true;
        $now = new \DateTimeImmutable('now');
        $this->published_at = $now->format('Y-m-d H:i:s');
    }

    public function depublier(): void
    {
        if (!$this->is_published) {
            throw new \RuntimeException('La galerie est déjà dépubliée.');
        }
        
        $this->is_published = false;
        $this->published_at = null;
    }

    public function setCoverPhoto(string $photo_id): void
    {
        $this->cover_photo_id = $photo_id;
    }
}