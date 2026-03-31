<?php
namespace photopro\core\application\ports\spi\repositoryInterfaces;

use photopro\core\domain\entities\patient\Patient;

interface PatientRepositoryInterface {
    public function findById(string $id): Patient;
}