<?php
namespace App\Libraries;
use GuzzleHttp\Client;

class GitHub {
  private Client $client;
  private string $clientId;
  private string $clientSecret;
  private string $redirectUri;

  public function __construct(string $clientId, string $clientSecret, string $redirectUri) {
    $this->clientId = $clientId;
    $this->clientSecret = $clientSecret;
    $this->redirectUri = $redirectUri;
    $this->client = new Client([
      'base_uri' => 'https://github.com/'
    ]);
  }

  public function getAccessToken(string $code, string $state): array {
    $response = $this->client->post('login/oauth/access_token', [
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

    $data = json_decode($response->getBody()->getContents(), true);
    if (isset($data['error'])) {
      throw new \Exception("Error fetching access token: " . $data['error_description']);
    }

    return $data;
  }
}