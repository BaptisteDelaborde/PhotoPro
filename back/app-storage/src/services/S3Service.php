<?php

namespace photopro\storage\services;

use Aws\S3\S3Client;
use Psr\Http\Message\StreamInterface;
use Ramsey\Uuid\Uuid;

class S3Service
{
    private S3Client $s3Client;
    private string $bucket;

    public function __construct()
    {
        $this->bucket = $_ENV['S3_BUCKET'];
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region'  => $_ENV['S3_REGION'],
            'endpoint' => $_ENV['S3_INTERNAL_ENDPOINT'],
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key'    => $_ENV['S3_ACCESS_KEY'],
                'secret' => $_ENV['S3_SECRET_KEY'],
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

        return [
            's3_key' => $s3Key,
            'url'    => $_ENV['S3_EXTERNAL_ENDPOINT'] . '/' . $this->bucket . '/' . $s3Key
        ];
    }
}