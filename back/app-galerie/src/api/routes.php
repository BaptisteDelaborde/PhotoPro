<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use photopro\api\actions\CreerRendezVousAction;
use photopro\api\actions\DetailPraticienAction;
use photopro\api\actions\GetPatientById;
use photopro\api\actions\GetRdvById;
use photopro\api\actions\HonorerRDVAction;
use photopro\api\actions\ListePraticiensAction;
use photopro\api\actions\ListerCreneauxOccupesAction;
use photopro\api\actions\AgendaPraticienAction;
use photopro\api\actions\AnnulerRDVAction;
use photopro\api\actions\NonHonorerRDVAction;
use photopro\api\middleware\AuthMiddleware;
use photopro\api\middleware\AuthzMiddleware;
use photopro\api\middleware\ValidateRDVMiddleware;
use photopro\api\actions\SigninAction;
use photopro\api\actions\HistoriquePatientAction;

return function( \Slim\App $app):\Slim\App {

    // Route pour lister tous les praticiens
    $app->get('/praticiens', ListePraticiensAction::class)->setName("praticiens.all");

    //Route pour liser le rdv correspondant à l'id rentré
    $app->get('/rdvs/{id}', GetRdvById::class)->setName("rdvs.id")->add(AuthzMiddleware::class)->add(AuthMiddleware::class);

    // Route pour obtenir les détails d'un praticien par son ID
    $app->get('/praticiens/{id}', DetailPraticienAction::class)->setName("praticien.id");

    // Route pour lister les créneaux occupés d'un praticien sur une période
    $app->get('/praticiens/{id}/rdvs', ListerCreneauxOccupesAction::class)->setName("praticien.crenaux");

    // Agenda praticien
    $app->get('/praticiens/{id}/agenda', AgendaPraticienAction::class)->setName("praticien.aganda");

    // Annulation de rendez-vous
    $app->get('/rdvs/{id}/annuler', AnnulerRDVAction::class)->setName("rdvs");

    // Changer le statut d'un rendez-vous honorer
    $app->get('/rdvs/{id}/honorer', HonorerRDVAction::class)->setName("rdvs.honorer");

    // Changer le statut d'un rendez-vous non honorer
    $app->get('/rdvs/{id}/non_honorer', NonHonorerRDVAction::class)->setName("rdvs.non.honorer");

    // Route pour obtenir un patient selon un id
    $app->get('/patients/{id}', GetPatientById::class)->setName("patients.id");

    //Route pour ajouter un Rendez-Vous
    $app->post('/rdvs', CreerRendezVousAction::class)->add(ValidateRDVMiddleware::class)->setName("rdv.ajout");

    // Auth
    $app->post('/signin', SigninAction::class)->setName('signin');

    // Route pour récupérer l'historique des consultations d'un patient
    $app->get('/patients/{id}/consultations',HistoriquePatientAction::class)->setName('patients.consultations')->add(AuthzMiddleware::class)->add(AuthMiddleware::class);;


    return $app;
};