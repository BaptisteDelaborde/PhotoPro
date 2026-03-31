<?php

use DI\Container;
use photopro\api\actions\CreerRendezVousAction;
use photopro\api\actions\DetailPraticienAction;
use photopro\api\actions\GetPatientById;
use photopro\api\actions\GetRdvById;
use photopro\api\actions\HonorerRDVAction;
use photopro\api\actions\ListePraticiensAction;
use photopro\api\actions\ListerCreneauxOccupesAction;
use photopro\api\actions\AgendaPraticienAction;
use photopro\api\actions\NonHonorerRDVAction;
use photopro\core\application\ports\api\ServicePatientInterface;
use photopro\api\actions\AnnulerRDVAction;
use photopro\core\application\ports\api\ServicePraticienInterface;
use photopro\core\application\ports\api\ServiceRdvInterface;
use photopro\api\actions\SigninAction;
use photopro\core\application\ports\api\ServiceUserInterface;
use photopro\core\application\ports\spi\repositoryInterfaces\PraticienRepositoryInterface;
use photopro\core\application\ports\spi\repositoryInterfaces\RdvRepositoryInterface;
use photopro\core\application\usecases\ServicePraticien;
use photopro\core\application\usecases\ServiceRdv;
use photopro\api\providers\AuthnProviderInterface;

return [

    // Liste des praticiens
    ListePraticiensAction::class => function ($c) {
        return new ListePraticiensAction(
            $c->get(ServicePraticienInterface::class)
        );
    },

    // Détail d’un praticien
    DetailPraticienAction::class => function ($c) {
        return new DetailPraticienAction(
            $c->get(ServicePraticienInterface::class)
        );
    },

    // Rendez-vous par ID
    GetRdvById::class => function ($c) {
        return new GetRdvById(
            $c->get(ServiceRdvInterface::class)
        );
    },

    // Créneaux occupés
    ListerCreneauxOccupesAction::class => function ($c) {
        return new ListerCreneauxOccupesAction(
            $c->get(ServiceRdvInterface::class)
        );
    },
    // Agenda praticien
    AgendaPraticienAction::class => function ($c) {
        return new AgendaPraticienAction(
            $c->get(ServiceRdvInterface::class)
        );
    },
    // Annuler un RDV
    AnnulerRDVAction::class => function ($c) {
        return new AnnulerRDVAction(
            $c->get(ServiceRdvInterface::class)
        );
    },
    // Change le statut du RDV à honorer
    HonorerRDVAction::class => function ($c) {
        return new HonorerRDVAction(
            $c->get(ServiceRdvInterface::class)
        );
    },
    // Change le statut du RDV à non honorer
    NonHonorerRDVAction::class => function ($c) {
        return new NonHonorerRDVAction(
            $c->get(ServiceRdvInterface::class)
        );
    },
    // Patient par ID
    GetPatientById::class =>function($c) {
        return new GetPatientById(
          $c->get(ServicePatientInterface::class)
        );
    },
    // Ajout d'un Rendez-Vous
    CreerRendezVousAction::class => function ($c){
        return new CreerRendezVousAction(
            $c->get(ServiceRdvInterface::class)
        );
    },

    // Signin
    SigninAction::class => function($c){
        return new SigninAction(
            $c->get(AuthnProviderInterface::class)
        );
    },
];
