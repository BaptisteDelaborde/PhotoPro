<?php

use gateway\api\actions\GenericGatewayAction;
use gateway\api\middleware\ValidateTokenMiddleware;
use Slim\App;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/', function ($request, $response) {
        $response->getBody()->write(json_encode([
            'message' => 'API Gateway PhotoPro Frontoffice',
            'endpoints' => [
                'GET /galeries'                              => 'Liste des galeries publiques (filtres: ?photographe_id=, ?sort=date)',
                'GET /galeries/{id}'                        => 'Détail d\'une galerie publique',
                'GET /galeries/{id}/photos'                 => 'Photos d\'une galerie publique',
                'GET /galeries/code/{code}'                 => 'Accès à une galerie privée par code',
                'GET /photographes'                         => 'Liste des photographes',
                'GET /photographes/{pseudo}/galeries'       => 'Galeries publiques d\'un photographe',
                'POST /galeries/{id}/photos/{photoId}/commentaires' => 'Ajouter un commentaire (anonyme)',
                'POST /auth/signin'                         => 'Connexion photographe (mode mobile)',
                'POST /auth/refresh'                        => 'Rafraîchissement de token',
                'POST /photographes/{id}/photos'            => 'Upload photo vers espace personnel (auth requise)',
            ]
        ], JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $validateTokenMiddleware = $container->get(ValidateTokenMiddleware::class);

    // Routes d'authentification (passthrough vers app-auth, pour le mode photographe mobile)
    $app->post('/auth/signin', GenericGatewayAction::class);
    $app->post('/auth/refresh', GenericGatewayAction::class);

    // Routes protégées (nécessitent un token JWT) : upload photographe depuis mobile
    $app->group('', function (\Slim\Routing\RouteCollectorProxy $group) {
        $group->post('/photographes/{id}/photos', GenericGatewayAction::class);
    })->add($validateTokenMiddleware::class);

    // Routes publiques : navigation galeries, accès galerie privée par code/URL, commentaires
    $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', GenericGatewayAction::class);

    return $app;
};
