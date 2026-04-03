<?php
namespace photopro\api\actions;

use photopro\core\application\ports\api\ServiceGalerieInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetUserGaleriesAction {
    public function __construct(private ServiceGalerieInterface $serviceGalerie) {}

    public function __invoke(Request $rq, Response $rs): Response {
        $userProfile = $rq->getAttribute('user_profile');
        $galeries = $this->serviceGalerie->getGaleriesByPhotographer($userProfile->id);
        
        $rs->getBody()->write(json_encode($galeries));
        return $rs->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
