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

    // Toutes les autres routes passent par le GenericGatewayAction
    $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', GenericGatewayAction::class)->add($validateTokenMiddleware::class);

    return $app;
};
