<?php

namespace photopro\storage\services;

use Aws\S3\S3Client;
use Psr\Http\Message\StreamInterface;

class S3Service
{
    private S3Client $s3Client;
    private string $bucket;

    public function __construct()
    {
        $this->bucket = (string) (getenv('S3_BUCKET') ?: 'photopro-galeries');
        
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region'  => (string) (getenv('S3_REGION') ?: 'seaweedFS'),
            'endpoint' => (string) (getenv('S3_INTERNAL_ENDPOINT') ?: 'http://S3:8333'),
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key'    => (string) (getenv('S3_ACCESS_KEY') ?: 'GHIJKL'),
                'secret' => (string) (getenv('S3_SECRET_KEY') ?: '789012'),
            ],
        ]);
    }

public function uploadFile(StreamInterface $stream, string $mimeType, string $extension): array
    {
        $fileName = uniqid('photo_') . '.' . $extension;
        $s3Key = 'uploads/' . date('Y/m/d') . '/' . $fileName;

        $this->s3Client->putObject([
            'Bucket'      => $this->bucket,
            'Key'         => $s3Key,
            'Body'        => $stream->detach(),
            'ContentType' => $mimeType,
        ]);

        $externalEndpoint = (string) (getenv('S3_EXTERNAL_ENDPOINT') ?: 'http://localhost:8333');
        
        $s3ExternalClient = new S3Client([
            'version' => 'latest',
            'region'  => (string) (getenv('S3_REGION') ?: 'seaweedFS'),
            'endpoint' => $externalEndpoint,
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key'    => (string) (getenv('S3_ACCESS_KEY') ?: 'GHIJKL'),
                'secret' => (string) (getenv('S3_SECRET_KEY') ?: '789012'),
            ],
        ]);

        $cmd = $s3ExternalClient->getCommand('GetObject', [
            'Bucket' => $this->bucket,
            'Key'    => $s3Key
        ]);

        $request = $s3ExternalClient->createPresignedRequest($cmd, '+60 minutes');
        $publicUrl = (string) $request->getUri();

        return [
            's3_key' => $s3Key,
            'url'    => $publicUrl
        ];
    }
    public function deleteFile(string $s3Key): void
    {
        $this->s3Client->deleteObject([
            'Bucket' => $this->bucket,
            'Key'    => $s3Key,
        ]);
    }

}