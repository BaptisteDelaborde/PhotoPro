<?php
namespace photopro\core\application\ports\spi\repositoryInterfaces;

use photopro\core\domain\entities\User;
interface AuthRepositoryInterface {
    public function findById (string $id): User;
}