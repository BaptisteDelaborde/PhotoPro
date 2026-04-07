<?php

use photopro\api\actions\AddCommentaireAction;
use photopro\api\actions\GetCommentairesAction;
use photopro\api\actions\GetPublicGaleriePhotosAction;
use Psr\Container\ContainerInterface;
use photopro\api\actions\AddPhotoAction;
use photopro\api\actions\CreateGalerieAction;
use photopro\api\actions\DeleteGalerieAction;
use photopro\api\actions\GetGalerieAction;
use photopro\api\actions\GetGalerieByCodeAction;
use photopro\api\actions\GetPhotosAction;
use photopro\api\actions\GetPublicGaleriesAction;
use photopro\api\actions\GetUserGaleriesAction;
use photopro\api\actions\UpdateGalerieStatusAction;
use photopro\api\actions\UpdateGalerieAction;
use photopro\api\middleware\AuthnMiddleware;
use photopro\core\application\ports\api\ServiceGalerieInterface;

return [
    AddPhotoAction::class => function (ContainerInterface $c) {
        return new AddPhotoAction($c->get(ServiceGalerieInterface::class));
    },

    CreateGalerieAction::class => function (ContainerInterface $c) {
        return new CreateGalerieAction($c->get(ServiceGalerieInterface::class));
    },

    DeleteGalerieAction::class => function (ContainerInterface $c) {
        return new DeleteGalerieAction($c->get(ServiceGalerieInterface::class));
    },

    GetGalerieAction::class => function (ContainerInterface $c) {
        return new GetGalerieAction($c->get(ServiceGalerieInterface::class));
    },

    GetGalerieByCodeAction::class => function (ContainerInterface $c) {
        return new GetGalerieByCodeAction($c->get(ServiceGalerieInterface::class));
    },

    GetPhotosAction::class => function (ContainerInterface $c) {
        return new GetPhotosAction($c);
    },

    GetPublicGaleriesAction::class => function (ContainerInterface $c) {
        return new GetPublicGaleriesAction($c->get(ServiceGalerieInterface::class));
    },

    GetPublicGaleriePhotosAction::class => function (ContainerInterface $c) {
        return new GetPublicGaleriePhotosAction($c->get(ServiceGalerieInterface::class));
    },

    GetUserGaleriesAction::class => function (ContainerInterface $c) {
        return new GetUserGaleriesAction($c->get(ServiceGalerieInterface::class));
    },

    UpdateGalerieStatusAction::class => function (ContainerInterface $c) {
        return new UpdateGalerieStatusAction($c->get(ServiceGalerieInterface::class));
    },

    UpdateGalerieAction::class => function (ContainerInterface $c) {
        return new UpdateGalerieAction($c->get(ServiceGalerieInterface::class));
    },

    AddCommentaireAction::class => function (ContainerInterface $c) {
        return new AddCommentaireAction($c->get(ServiceGalerieInterface::class));
    },

    GetCommentairesAction::class => function (ContainerInterface $c) {
        return new GetCommentairesAction($c->get(ServiceGalerieInterface::class));
    },

        \photopro\api\actions\DeletePhotoAction::class => function (\Psr\Container\ContainerInterface $c) {
        return new \photopro\api\actions\DeletePhotoAction($c->get(ServiceGalerieInterface::class));
    },

    // Middlewares
    AuthnMiddleware::class => function (ContainerInterface $c) {
        $secretKey = $_ENV['JWT_SECRET_KEY'] ?? 'cle_secrete';
        return new AuthnMiddleware($secretKey);
    }
];
