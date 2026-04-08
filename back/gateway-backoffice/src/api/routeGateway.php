<?php

use gateway\api\actions\GenericGatewayAction;
use Slim\App;
use gateway\api\middleware\ValidateTokenMiddleware;
use gateway\api\actions\UploadPhotoGatewayAction;
use gateway\api\actions\DeletePhotoGatewayAction;
use gateway\api\actions\UpdateGalerieGatewayAction;
use gateway\api\actions\UploadGlobalPhotoGatewayAction;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/', function ($request, $response) {
        $response->getBody()->write(json_encode([
            'message' => 'API Gateway PhotoPro Backoffice',
            'endpoints' => [
                '/auth/*' => 'Service d\'authentification (signin, signup, refresh, tokens, etc)',
                '/galerie/*' => 'Service galerie',
            ]
        ], JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $validateTokenMiddleware = $container->get(ValidateTokenMiddleware::class);

    $app->post('/auth/signin', GenericGatewayAction::class);
    $app->post('/auth/signup', GenericGatewayAction::class);
    $app->post('/auth/refresh', GenericGatewayAction::class);

    $app->group('', function (\Slim\Routing\RouteCollectorProxy $group) {
        
        $group->post('/photographes/{id}/galeries/{galerie_id}/photos', UploadPhotoGatewayAction::class);
        $group->delete('/photographes/{id}/galeries/{galerie_id}/photos/{photo_id}', DeletePhotoGatewayAction::class);

        $group->put('/photographes/{id}/galeries/{galerie_id}', GenericGatewayAction::class);
        $group->delete('/photographes/{id}/galeries/{galerie_id}', GenericGatewayAction::class);        
        
        $group->post('/photographes/{id}/photos', UploadGlobalPhotoGatewayAction::class);
        $group->get('/photographes/{id}/photos', GenericGatewayAction::class);
        
    })->add($validateTokenMiddleware::class);

    $app->group('', function (\Slim\Routing\RouteCollectorProxy $group) {
        $group->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', GenericGatewayAction::class);
    })->add($validateTokenMiddleware::class);

    return $app;
};