<?php
declare(strict_types=1);

use photopro\api\actions\GetPhotographesAction;
use photopro\api\actions\GetPublicGaleriePhotosAction;
use photopro\api\actions\GetPublicGaleriesAction;
use photopro\api\actions\GetGalerieByCodeAction;
use photopro\api\actions\LinkPhotoToGalerieAction;
use photopro\api\middleware\AuthnMiddleware;
use photopro\api\actions\AddPhotoAction;
use photopro\api\actions\GetPhotosAction;
use photopro\api\actions\CreateGalerieAction;
use photopro\api\actions\GetUserGaleriesAction;
use photopro\api\actions\GetGalerieAction;
use photopro\api\actions\UpdateGalerieStatusAction;
use photopro\api\actions\UpdateGalerieAction;
use photopro\api\actions\DeleteGalerieAction;
use photopro\api\actions\AddCommentaireAction;
use photopro\api\actions\GetCommentairesAction;
use photopro\api\actions\GetPhotographerPhotosAction;
use photopro\api\actions\AddPhotoToStorageAction;

return function (\Slim\App $app): \Slim\App {

    // --- Gestion des Photos ---
    $app->post('/photographes/{id}/galeries/{galerie_id}/photos', AddPhotoAction::class)
        ->add(AuthnMiddleware::class);

    $app->get('/photographes/{id}/galeries/{galerie_id}/photos', GetPhotosAction::class)
        ->add(AuthnMiddleware::class);

    // --- Routes Publiques ---
    $app->get('/galeries/publiques', GetPublicGaleriesAction::class);
    $app->get('/galeries/code/{code}', GetGalerieByCodeAction::class);
    $app->get('/galeries/{id}/photos', GetPublicGaleriePhotosAction::class);
    $app->post('/galeries/{id}/photos/{photo_id}/commentaires', AddCommentaireAction::class);
    $app->get('/galeries/{id}/photos/{photo_id}/commentaires', GetCommentairesAction::class);
    $app->patch('/photographes/{id}/photos/{photo_id}', LinkPhotoToGalerieAction::class)->add(AuthnMiddleware::class);
    $app->get('/photographes', GetPhotographesAction::class);

    // --- Gestion des Galeries ---
    $app->post('/galeries', CreateGalerieAction::class)
        ->add(AuthnMiddleware::class);
    $app->get('/galeries', GetUserGaleriesAction::class)
        ->add(AuthnMiddleware::class);
    $app->get('/galeries/{id}', GetGalerieAction::class)
        ->add(AuthnMiddleware::class);
    $app->patch('/galeries/{id}/status', UpdateGalerieStatusAction::class)
        ->add(AuthnMiddleware::class);
    $app->put('/galeries/{id}', UpdateGalerieAction::class)
        ->add(AuthnMiddleware::class);
    $app->delete('/photographes/{id}/galeries/{galerie_id}', DeleteGalerieAction::class)
        ->add(AuthnMiddleware::class);
    $app->delete('/photographes/{id}/galeries/{galerie_id}/photos/{photo_id}', \photopro\api\actions\DeletePhotoAction::class)
        ->add(AuthnMiddleware::class);

    $app->post('/photographes/{id}/photos', \photopro\api\actions\AddPhotoToStorageAction::class)->add(AuthnMiddleware::class);
    $app->get('/photographes/{id}/photos', \photopro\api\actions\GetPhotographerPhotosAction::class)->add(AuthnMiddleware::class);

    return $app;
};