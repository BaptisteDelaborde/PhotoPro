<?php
namespace photopro\core\application\ports\api;

class AuthDTO {
    public function __construct(
        public string $accesToken,
        public string $refreshToken
    )
    {}
}
