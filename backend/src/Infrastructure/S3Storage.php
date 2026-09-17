<?php

namespace App\Infrastructure;

use AsyncAws\S3\S3Client;
use AsyncAws\S3\Input\GetObjectRequest;
use AsyncAws\Core\Exception\Http\HttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * async-aws/s3 を用いた S3（互換）ストレージ操作。
 *
 * hostUrl はエンドポイント（AWS なら空でも可、R2/MinIO 等は独自URL）。
 * clientId / clientSecret は AWS のアクセスキーID / シークレットに対応する。
 */
class S3Storage
{
    private S3Client $client;
    private string $hostUrl;
    private string $bucket;
    private string $region;
    private string $clientId;
    private string $clientSecret;

    public function __construct(
        string $hostUrl,
        string $bucket,
        string $region,
        string $clientId,
        string $clientSecret,
        ?HttpClientInterface $httpClient = null
    ) {
        $this->hostUrl = $hostUrl;
        $this->bucket = $bucket;
        $this->region = $region;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;

        $config = [
            'region' => $region,
            'accessKeyId' => $clientId,
            'accessKeySecret' => $clientSecret,
        ];
        if ($hostUrl !== '') {
            // S3 互換サービス（R2 / MinIO など）は独自エンドポイント + path-style
            $config['endpoint'] = $hostUrl;
            $config['pathStyleEndpoint'] = true;
        }

        $this->client = new S3Client($config, null, $httpClient);
    }

    /**
     * オブジェクトを保存する。
     *
     * @param array<string, mixed> $options putObject への追加パラメータ（ACL など）
     */
    public function put(string $key, string $content, ?string $contentType = null, array $options = []): void
    {
        $params = array_merge([
            'Bucket' => $this->bucket,
            'Key' => $key,
            'Body' => $content,
        ], $options);

        if ($contentType !== null) {
            $params['ContentType'] = $contentType;
        }

        try {
            $this->client->putObject($params)->resolve();
        } catch (HttpException $e) {
            throw new InfrastructureException(
                "Failed to put object: {$key}",
                $e->getResponse()->getStatusCode(),
                $e->getResponse()->getContent(false),
                $e
            );
        }
    }

    /**
     * オブジェクトの中身を文字列で取得する。
     */
    public function get(string $key): string
    {
        try {
            $result = $this->client->getObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
            ]);

            return $result->getBody()->getContentAsString();
        } catch (HttpException $e) {
            throw new InfrastructureException(
                "Failed to get object: {$key}",
                $e->getResponse()->getStatusCode(),
                $e->getResponse()->getContent(false),
                $e
            );
        }
    }

    /**
     * オブジェクトを削除する。
     */
    public function delete(string $key): void
    {
        try {
            $this->client->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
            ])->resolve();
        } catch (HttpException $e) {
            throw new InfrastructureException(
                "Failed to delete object: {$key}",
                $e->getResponse()->getStatusCode(),
                $e->getResponse()->getContent(false),
                $e
            );
        }
    }

    /**
     * オブジェクトが存在するか。
     */
    public function exists(string $key): bool
    {
        return $this->client->objectExists([
            'Bucket' => $this->bucket,
            'Key' => $key,
        ])->isSuccess();
    }

    /**
     * オブジェクトの公開URL（path-style）。バケットが公開設定の場合に使う。
     */
    public function url(string $key): string
    {
        return rtrim($this->hostUrl, '/') . '/' . $this->bucket . '/' . ltrim($key, '/');
    }

    /**
     * 署名付きGET URL を発行する。
     *
     * @param string $expires 有効期限（strtotime 互換、例 "+15 minutes"）
     */
    public function presignedUrl(string $key, string $expires = '+15 minutes'): string
    {
        $input = new GetObjectRequest([
            'Bucket' => $this->bucket,
            'Key' => $key,
        ]);

        return $this->client->presign($input, new \DateTimeImmutable($expires));
    }
}
