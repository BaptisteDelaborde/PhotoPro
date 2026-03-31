<?php

namespace photopro\core\application\usecases;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Psr\Http\Message\StreamInterface;
use Ramsey\Uuid\Uuid;

class StorageService
{
    private S3Client $s3_internal_client;
    private S3Client $s3_external_client;
    private string $bucket;

    public function __construct(S3Client $s3_internal_client, S3Client $s3_external_client, string $bucket) {
        $this->s3_internal_client = $s3_internal_client;
        $this->s3_external_client = $s3_external_client;
        $this->bucket = $bucket;
    }

    public function store(string $photographer_id, StreamInterface $content, string $mime_type): string {
        $uuid = Uuid::uuid4()->toString();
        $ext = $this->mimeToExtension($mime_type);
        
        $key = sprintf('users/%s/%s.%s', $photographer_id, $uuid, $ext);

        try {
            $this->s3_internal_client->putObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
                'Body' => $content,
                'ContentType' => $mime_type,
                'Metadata' => [
                    'date' => date('d/m/Y H:i:s'),
                ]
            ]);
        } catch (S3Exception $e) {
            throw new \Exception('Erreur lors du stockage S3 : ' . $e->getMessage(), (int)$e->getCode(), $e);
        }

        return $key;
    }

    public function getPresignedUrl(string $key, int $expiresInSeconds = 3600): string {
        $command = $this->s3_external_client->getCommand('GetObject', [
            'Bucket' => $this->bucket,
            'Key' => $key
        ]);

        $request = $this->s3_external_client->createPresignedRequest($command, "+{$expiresInSeconds} seconds");
        
        return (string) $request->getUri();
    }

    private function mimeToExtension(string $mimeType): string {
        return match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => 'bin',
        };
    }
}