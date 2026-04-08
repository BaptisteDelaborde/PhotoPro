<?php
namespace photopro\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use photopro\core\application\ports\spi\repositoryInterfaces\AuthRepositoryInterface;
use photopro\core\application\ports\api\ServiceUserInterface;

class UpdatePhotographeAction
{
    private ServiceUserInterface $serviceUser;
    public function __construct(ServiceUserInterface $serviceUser)
    {
        $this->serviceUser = $serviceUser;
    }
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $photographeId = $args['id'];
        $data = $request->getParsedBody() ?? [];

        try {
            // ON APPELLE LE SERVICE
            $this->serviceUser->updateProfile($photographeId, $data);

            $response->getBody()->write(json_encode(['message' => 'Profil mis à jour avec succès']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }
    }
}