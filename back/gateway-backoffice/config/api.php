<?php

use DI\Container;
use GuzzleHttp\Client;
use gateway\api\actions\GenericGatewayAction;
use gateway\api\middleware\ValidateTokenMiddleware;

return [
    'client.galerie' => function (Container $c) {
        $settings = $c->get('settings');
        return new Client([
            'base_uri' => $settings['services']['api_galerie'],
        ]);
    },

    'client.auth' => function (Container $c) {
        $settings = $c->get('settings');
        return new Client([
            'base_uri' => $settings['services']['api_auth'],
        ]);
    },

    'client.storage' => function (Container $c) {
        $settings = $c->get('settings');
        return new Client([
            'base_uri' => $settings['services']['api_storage'],
        ]);
    },

    GenericGatewayAction::class => function (Container $c) {
        return new GenericGatewayAction(
            $c->get('client.galerie'),
            $c->get('client.auth')
        );
    },


    ValidateTokenMiddleware::class => function (Container $c) {
        return new ValidateTokenMiddleware(
            $c->get('client.auth')
        );
    },
];
