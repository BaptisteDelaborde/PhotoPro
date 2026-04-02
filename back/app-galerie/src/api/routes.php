<?php
declare(strict_types=1);

use photopro\api\middleware\AuthnMiddleware;
use photopro\api\actions\UploadAction;
use photopro\api\actions\GetPhotosAction;
use photopro\api\actions\CreateGalerieAction;
use photopro\api\actions\GetUserGaleriesAction;
use photopro\api\actions\GetGalerieAction;
use photopro\api\actions\UpdateGalerieStatusAction;
use photopro\api\actions\DeleteGalerieAction;

return function (\Slim\App $app): \Slim\App {

    // --- Gestion des Photos ---
    $app->post('/photographes/{id}/photos', UploadAction::class)
        ->add(AuthnMiddleware::class);
    $app->get('/photographes/{id}/photos', GetPhotosAction::class)
        ->add(AuthnMiddleware::class);

    // --- Gestion des Galeries ---
    $app->post('/galeries', CreateGalerieAction::class)
        ->add(AuthnMiddleware::class);
    $app->get('/galeries', GetUserGaleriesAction::class)
        ->add(AuthnMiddleware::class);
    $app->get('/galeries/{id}', GetGalerieAction::class)
        ->add(AuthnMiddleware::class);
    $app->patch('/galeries/{id}/status', UpdateGalerieStatusAction::class)
        ->add(AuthnMiddleware::class);
    $app->delete('/galeries/{id}', DeleteGalerieAction::class)
        ->add(AuthnMiddleware::class);


    // --- Routes Publique ---
    // $app->get('/galeries/publiques', GetPublicGaleriesAction::class);
    // $app->get('/galeries/code/{code}', GetGalerieByCodeAction::class);

    return $app;
};