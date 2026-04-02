<?php

namespace photopro\api\actions;

use photopro\core\application\ports\api\ServiceGalerieInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetGalerieByCodeAction
{
    public function __construct(private ServiceGalerieInterface $serviceGalerie) {}

    public function __invoke(Request $rq, Response $rs, array $args): Response
    {
        $code = $args['code'];

        try {
            $galerieDTO = $this->serviceGalerie->getGalerieByCode($code);
        } catch (\Exception $e) {
            $status = $e->getCode() ?: 404;
            $rs->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $rs->withHeader('Content-Type', 'application/json')->withStatus($status);
        }

        $rs->getBody()->write(json_encode($galerieDTO));
        return $rs->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
