<?php
declare(strict_types=1);

namespace photopro\api\actions;

use photopro\core\application\ports\api\ServiceGalerieInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetPublicGaleriesAction {

    public function __construct(private ServiceGalerieInterface $serviceGalerie) {}

    public function __invoke(Request $rq, Response $rs, array $args): Response {
        $queryParams = $rq->getQueryParams();

        $photographerId = !empty($queryParams['photographer_id']) ? $queryParams['photographer_id'] : null;

        $galeries = $this->serviceGalerie->getPublicGaleries($photographerId);

        $rs->getBody()->write(json_encode($galeries));
        return $rs->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
