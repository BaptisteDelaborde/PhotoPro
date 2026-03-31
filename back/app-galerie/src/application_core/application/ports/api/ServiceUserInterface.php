<?php

namespace photopro\core\application\ports\api;

interface ServiceUserInterface{

    public function register(CredentialsDTO $credentials, int $role): ProfileDTO;
    public function byCredentials(CredentialsDTO $credentials): ?ProfileDTO;
}