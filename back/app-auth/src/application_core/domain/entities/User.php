<?php
namespace photopro\core\domain\entities;

class User
{
    private string $id;
    private string $email;
    private string $password;
    private int $role;
    private array $profileData = [];

    public function __construct(string $id, string $email, string $password, int $role)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    public function setProfileData(array $data): void
    {
        $this->profileData = $data;
    }
    public function getProfileData(): array
    {
        return $this->profileData;
    }

    public function getId(): string
    {
        return $this->id;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getPassword(): string
    {
        return $this->password;
    }
    public function getRole(): int
    {
        return $this->role;
    }
}