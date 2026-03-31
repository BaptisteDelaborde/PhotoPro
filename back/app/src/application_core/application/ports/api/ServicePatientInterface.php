<?php
namespace photopro\core\application\ports\api;


interface ServicePatientInterface
{
    public function getPatient(string $id): ?PatientDTO;
}