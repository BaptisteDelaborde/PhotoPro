<?php

namespace photopro\core\application\ports\api;

class GalerieDTO
{
    public string $id;
    public string $photographer_id;
    public string $title;
    public string $layout;
    public bool $is_public;
    public bool $is_published;
    public string $created_at;
    public ?string $description;
    public ?string $cover_photo_id;
    public ?string $access_code;
    public ?string $access_url;
    public ?string $client_name;
    public ?string $client_email;
    public ?string $published_at;

    public function __construct(
        string $id,
        string $photographer_id,
        string $title,
        string $layout,
        bool $is_public,
        bool $is_published,
        string $created_at,
        ?string $description = null,
        ?string $cover_photo_id = null,
        ?string $access_code = null,
        ?string $access_url = null,
        ?string $client_name = null,
        ?string $client_email = null,
        ?string $published_at = null
    ) {
        $this->id = $id;
        $this->photographer_id = $photographer_id;
        $this->title = $title;
        $this->layout = $layout;
        $this->is_public = $is_public;
        $this->is_published = $is_published;
        $this->created_at = $created_at;
        $this->description = $description;
        $this->cover_photo_id = $cover_photo_id;
        $this->access_code = $access_code;
        $this->access_url = $access_url;
        $this->client_name = $client_name;
        $this->client_email = $client_email;
        $this->published_at = $published_at;
    }
}