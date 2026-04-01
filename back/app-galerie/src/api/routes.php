<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use photopro\api\actions\UploadAction;
use photopro\api\actions\CreateGalerieAction;
use photopro\api\middleware\AuthnMiddleware;

return function( \Slim\App $app):\Slim\App {
    
    $app->post('/photographes/{id}/photos', UploadAction::class)
        ->add(AuthnMiddleware::class);

    $app->post('/galeries', CreateGalerieAction::class)
        ->add(AuthnMiddleware::class);

    return $app;
};