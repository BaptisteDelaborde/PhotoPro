<?php

namespace photopro\api\actions;

use photopro\core\application\ports\api\ServiceGalerieInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetCommentairesAction
{
    public function __construct(private ServiceGalerieInterface $serviceGalerie) {}

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $photoId = $args['photo_id'];

        $commentaires = $this->serviceGalerie->getCommentaires($photoId);

        $response->getBody()->write(json_encode($commentaires));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
