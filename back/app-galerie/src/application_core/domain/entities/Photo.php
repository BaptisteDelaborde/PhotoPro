<?php

namespace photopro\core\domain\entities;

class Photo
{
    private string $id;
    private string $photographer_id;
    private ?string $title;
    private string $file_name;
    private string $mime_type;
    private float $file_size;
    private string $storage_url;
    private string $uploaded_at;
    private ?string $galerie_id;

    public function __construct(
        string $id,
        string $photographer_id,
        ?string $galerie_id,
        string $file_name,
        string $mime_type,
        float $file_size,
        string $storage_url,
        string $uploaded_at,
        ?string $title = null
    ) {
        $this->id = $id;
        $this->photographer_id = $photographer_id;
        $this->galerie_id = $galerie_id;
        $this->file_name = $file_name;
        $this->mime_type = $mime_type;
        $this->file_size = $file_size;
        $this->storage_url = $storage_url;
        $this->uploaded_at = $uploaded_at;
        $this->title = $title;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPhotographerId(): string
    {
        return $this->photographer_id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getFileName(): string
    {
        return $this->file_name;
    }

    public function getMimeType(): string
    {
        return $this->mime_type;
    }

    public function getFileSize(): float
    {
        return $this->file_size;
    }

    public function getStorageUrl(): string
    {
        return $this->storage_url;
    }

    public function getUploadedAt(): string
    {
        return $this->uploaded_at;
    }
    public function getGalerieId(): string
    {
        return $this->galerie_id;
    }
}