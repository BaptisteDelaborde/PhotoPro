<?php

namespace photopro\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use photopro\core\application\ports\api\ServiceGalerieInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpInternalServerErrorException;

class AddPhotoAction
{
    private ServiceGalerieInterface $serviceGalerie;
    
    public function __construct(ServiceGalerieInterface $serviceGalerie) {
        $this->serviceGalerie = $serviceGalerie;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $photographer_id = $args['id']; 
        $galerie_id = $args['galerie_id'];
        
        $data = $request->getParsedBody();

        if (empty($data['file_name']) || empty($data['mime_type']) || empty($data['file_size']) || empty($data['s3_key'])) {
            throw new HttpBadRequestException($request, "Données manquantes. Le JSON doit contenir file_name, mime_type, file_size et s3_key.");
        }

        try {
            $photo = $this->serviceGalerie->ajouterPhoto(
                $photographer_id,
                $galerie_id,
                $data['file_name'],
                $data['mime_type'],
                (float) $data['file_size'],
                $data['s3_key']
            );
            
        } catch (\Exception $e) {
            throw new HttpInternalServerErrorException($request, 'Erreur base de données : ' . $e->getMessage());
        }

        $response->getBody()->write(json_encode([
            'message' => 'Photo enregistrée en base de données avec succès !',
            'photo_id' => $photo->getId()
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }
}