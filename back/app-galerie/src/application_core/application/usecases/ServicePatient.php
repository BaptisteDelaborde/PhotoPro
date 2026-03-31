<?php
namespace photopro\core\application\usecases;

use photopro\core\application\ports\api\PatientDTO;
use photopro\core\application\ports\api\ServicePatientInterface;
use photopro\core\application\ports\spi\repositoryInterfaces\PatientRepositoryInterface;

class ServicePatient implements ServicePatientInterface
{
    private PatientRepositoryInterface $patientRepository;

    public function __construct(PatientRepositoryInterface $patientRepository)
    {
        $this->patientRepository = $patientRepository;
    }
    public function getPatient(string $id): ?PatientDTO
    {
        $patient = $this->patientRepository->findById($id);
        return new PatientDTO(
            $patient->getId(),
            $patient->getNom(),
            $patient->getPrenom(),
            $patient->getDateNaissance(),
            $patient->getAdresse(),
            $patient->getCodePostal(),
            $patient->getVille(),
            $patient->getEmail(),
            $patient->getTelephone()
        );
    }
}