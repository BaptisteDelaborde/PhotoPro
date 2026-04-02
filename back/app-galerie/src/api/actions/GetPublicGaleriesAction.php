<?php
declare(strict_types=1);

namespace photopro\api\actions;

use photopro\core\application\ports\api\ServiceGalerieInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetPublicGaleriesAction {

    public function __construct(private ServiceGalerieInterface $serviceGalerie) {}

    public function __invoke(Request $request, Response $response, array $args): Response {
        try {
            $galeries = $this->serviceGalerie->getPublicGaleries();
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }

        $response->getBody()->write(json_encode($galeries));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
