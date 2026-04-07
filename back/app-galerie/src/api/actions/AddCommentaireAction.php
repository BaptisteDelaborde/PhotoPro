<?php

namespace photopro\api\actions;

use photopro\core\application\ports\api\ServiceGalerieInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class AddCommentaireAction
{
    public function __construct(private ServiceGalerieInterface $serviceGalerie) {}

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $photoId = $args['photo_id'];
        $data = $request->getParsedBody();

        if (empty($data['content'])) {
            throw new HttpBadRequestException($request, "Le champ 'content' est requis.");
        }

        $commentaire = $this->serviceGalerie->addCommentaire(
            $photoId,
            $data['content'],
            $data['author_name'] ?? null
        );

        $response->getBody()->write(json_encode($commentaire));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}
