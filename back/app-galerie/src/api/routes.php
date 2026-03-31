<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use photopro\api\actions\UploadAction;


return function( \Slim\App $app):\Slim\App {
$app->post('/photographes/{id}/photos', UploadAction::class);


    return $app;
};