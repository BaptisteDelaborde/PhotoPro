<?php
declare(strict_types=1);

namespace photopro\api\actions;

use photopro\core\application\ports\api\ServiceGalerieInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetPhotographesAction {

    public function __construct(private ServiceGalerieInterface $serviceGalerie) {}

    public function __invoke(Request $rq, Response $rs, array $args): Response {
        $photographes = $this->serviceGalerie->getPhotographes();

        $rs->getBody()->write(json_encode($photographes));
        return $rs->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}