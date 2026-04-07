<?php
declare(strict_types=1);
namespace photopro\core\application\ports\api;

class ProfileDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly int $role,
        public readonly ?string $pseudo = null,
        public readonly ?string $firstName = null,
        public readonly ?string $lastName = null,
        public readonly ?string $phone = null
    ) {
    }
}