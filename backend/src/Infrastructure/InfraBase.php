<?php
namespace App\Infrastructure;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * 外部 HTTP API クライアントの共通基盤。
 *
 * サブクラスは base_uri を渡して継承し、get()/post() などのヘルパーで
 * リクエストする。$uri は Guzzle の URI 解決に従い、相対パスは base_uri に
 * 解決され、絶対URL（別ホスト）を渡すと base_uri を無視して絶対URLを使う。
 */
abstract class InfraBase
{
    protected Client $client;

    /**
     * @param array<string, mixed> $config Guzzle クライアント設定の上書き
     */
    public function __construct(string $baseUri = '', array $config = [])
    {
        $this->client = new Client(array_merge([
            'base_uri' => $baseUri,
            'timeout' => 10,
            'connect_timeout' => 5,
            'http_errors' => false,
            'headers' => $this->defaultHeaders(),
        ], $config));
    }

    /**
     * 全リクエストに付与する既定ヘッダ。サブクラスでオーバーライド可能。
     *
     * @return array<string, string>
     */
    protected function defaultHeaders(): array
    {
        return ['Accept' => 'application/json'];
    }

    /**
     * HTTP リクエストを送り、JSON デコード済みの配列を返す。
     *
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    protected function request(string $method, string $uri, array $options = []): array
    {
        try {
            $response = $this->client->request($method, $uri, $options);
        } catch (GuzzleException $e) {
            throw new InfrastructureException($e->getMessage(), null, null, $e);
        }

        // ボディはストリームなので1度だけ読み、以降は文字列/配列を使い回す
        $body = $response->getBody()->getContents();
        $data = $this->decode($body);

        // サブクラス固有のペイロードエラー（HTTP 200 でも error を返す OAuth など）
        $this->checkError($data, $response);

        // 汎用フォールバック: HTTP ステータスによるエラー
        $status = $response->getStatusCode();
        if ($status >= 400) {
            throw new InfrastructureException(
                $data['message'] ?? $data['error_description'] ?? "HTTP {$status}",
                $status,
                $body
            );
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    protected function get(string $uri, array $options = []): array
    {
        return $this->request('GET', $uri, $options);
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    protected function post(string $uri, array $options = []): array
    {
        return $this->request('POST', $uri, $options);
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    protected function put(string $uri, array $options = []): array
    {
        return $this->request('PUT', $uri, $options);
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    protected function patch(string $uri, array $options = []): array
    {
        return $this->request('PATCH', $uri, $options);
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    protected function delete(string $uri, array $options = []): array
    {
        return $this->request('DELETE', $uri, $options);
    }

    /**
     * レスポンスボディ（JSON 文字列）をデコードする。
     *
     * @return array<string, mixed>
     */
    protected function decode(string $body): array
    {
        return json_decode($body, true) ?? [];
    }

    /**
     * サブクラス固有のペイロードエラー判定フック。既定は何もしない。
     * エラーとみなす場合は InfrastructureException を投げる。
     *
     * @param array<string, mixed> $data
     */
    protected function checkError(array $data, ResponseInterface $response): void
    {
    }
}
