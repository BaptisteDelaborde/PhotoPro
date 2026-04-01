<?php

use gateway\api\actions\GenericGatewayAction;
use Slim\App;
use gateway\api\middleware\ValidateTokenMiddleware;


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

    //Routes publiques (sans vérif de token)
    $app->post('/auth/signin', GenericGatewayAction::class);
    $app->post('/auth/signup', GenericGatewayAction::class);
    $app->post('/auth/refresh', GenericGatewayAction::class);

    //Autres routes qui passent par le GenericGatewayAction avec le middleware de validation de token
    $app->group('', function (\Slim\Routing\RouteCollectorProxy $group) {
        $group->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', GenericGatewayAction::class);
    })->add($validateTokenMiddleware::class);

    return $app;
};
