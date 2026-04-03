<?php

namespace photopro\api\actions;

use photopro\core\application\ports\api\ServiceGalerieInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpBadRequestException;

class UpdateGalerieAction
{
    private ServiceGalerieInterface $serviceGalerie;

    public function __construct(ServiceGalerieInterface $serviceGalerie)
    {
        $this->serviceGalerie = $serviceGalerie;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = $args['id'];
        $body = $request->getParsedBody() ?? [];

        try {
            $dto = $this->serviceGalerie->updateGalerie($id, $body);
        } catch (\InvalidArgumentException $e) {
            throw new HttpBadRequestException($request, $e->getMessage());
        } catch (\Exception $e) {
            if ($e->getCode() === 404) {
                throw new HttpNotFoundException($request, $e->getMessage());
            }
            throw $e;
        }

        $response->getBody()->write(json_encode($dto));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
