<?php

namespace photopro\application_core\domain\entities;

class Photographe {

    private string $id;
    private string $auth_user_id;
    private string $pseudo;
    private string $first_name;
    private string $last_name;
    private string $email;
    private ?string $phone;
    private ?string $description;
    private ?string $profile_image_url;
    private string $created_at;

    public function __construct(
        string $id,
        string $auth_user_id,
        string $pseudo,
        string $first_name,
        string $last_name,
        string $email,
        ?string $phone = null,
        ?string $description = null,
        ?string $profile_image_url = null,
        string $created_at = ''
    ) {
        $this->id = $id;
        $this->auth_user_id = $auth_user_id;
        $this->pseudo = $pseudo;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->phone = $phone;
        $this->description = $description;
        $this->profile_image_url = $profile_image_url;
        $this->created_at = $created_at;
    }

    public function getId(): string {
        return $this->id;
    }

    public function getAuthUserId(): string {
        return $this->auth_user_id;
    }

    public function getPseudo(): string {
        return $this->pseudo;
    }

    public function getFirstName(): string {
        return $this->first_name;
    }

    public function getLastName(): string {
        return $this->last_name;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getPhone(): ?string {
        return $this->phone;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function getProfileImageUrl(): ?string {
        return $this->profile_image_url;
    }

    public function getCreatedAt(): string {
        return $this->created_at;
    }
}
