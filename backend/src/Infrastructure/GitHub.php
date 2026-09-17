<?php
namespace App\Infrastructure;

use Psr\Http\Message\ResponseInterface;

class GitHub extends InfraBase
{
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;

    public function __construct(string $clientId, string $clientSecret, string $redirectUri)
    {
        parent::__construct('https://github.com/');
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
    }

    /**
     * @return array<string, mixed>
     */
    public function getAccessToken(string $code, string $state): array
    {
        return $this->post('login/oauth/access_token', [
            'form_params' => [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
                'state' => $state
            ],
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getProfile(string $accessToken): array
    {
        return $this->get('https://api.github.com/user', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/vnd.github.v3+json'
            ]
        ]);
    }

    /**
     * OAuth は HTTP 200 でも error フィールドでエラーを返すため、ここで拾う。
     *
     * @param array<string, mixed> $data
     */
    protected function checkError(array $data, ResponseInterface $response): void
    {
        if (isset($data['error'])) {
            throw new InfrastructureException(
                $data['error_description'] ?? $data['error'],
                $response->getStatusCode()
            );
        }
    }
}
