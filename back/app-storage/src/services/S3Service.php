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
            'region' => (string) (getenv('S3_REGION') ?: 'seaweedFS'),
            'endpoint' => (string) (getenv('S3_INTERNAL_ENDPOINT') ?: 'http://S3:8333'),
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => (string) (getenv('S3_ACCESS_KEY') ?: 'GHIJKL'),
                'secret' => (string) (getenv('S3_SECRET_KEY') ?: '789012'),
            ],
        ]);

        try {
            $policy = json_encode([
                "Version" => "2012-10-17",
                "Statement" => [
                    [
                        "Sid" => "PublicRead",
                        "Effect" => "Allow",
                        "Principal" => "*",
                        "Action" => ["s3:GetObject"],
                        "Resource" => ["arn:aws:s3:::" . $this->bucket . "/*"]
                    ]
                ]
            ]);

            $this->s3Client->putBucketPolicy([
                'Bucket' => $this->bucket,
                'Policy' => $policy
            ]);
        } catch (\Exception $e) {
            error_log("ERREUR FATALE BUCKET POLICY : " . $e->getMessage());
        }
    }

    public function uploadFile(StreamInterface $stream, string $mimeType, string $extension): array
    {
        $fileName = uniqid('photo_') . '.' . $extension;
        $s3Key = 'uploads/' . date('Y/m/d') . '/' . $fileName;

        $this->s3Client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $s3Key,
            'Body' => $stream->detach(),
            'ContentType' => $mimeType,
            'ACL' => 'public-read',
        ]);
        $externalEndpoint = rtrim((string) (getenv('S3_EXTERNAL_ENDPOINT') ?: 'http://localhost:8333'), '/');

        $bucketName = trim($this->bucket, '/');

        return [
            's3_key' => $s3Key,
            'url' => $externalEndpoint . '/' . $bucketName . '/' . $s3Key
        ];
    }

    public function deleteFile(string $s3Key): void
    {
        $this->s3Client->deleteObject([
            'Bucket' => $this->bucket,
            'Key' => $s3Key,
        ]);
    }
}