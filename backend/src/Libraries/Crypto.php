<?php
namespace App\Libraries;

enum Algorithm: string
{
  case HS256 = 'HS256';
  case HS384 = 'HS384';
  case HS512 = 'HS512';
}

class Crypto
{
  public static function generateRandomString($length = 32)
  {
    return bin2hex(random_bytes($length / 2));
  }

  private static function base64UrlEncode(string $data)
  {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
  }

  private static function base64UrlDecode(string $data)
  {
    $remainder = strlen($data) % 4;
    if ($remainder) {
      $padlen = 4 - $remainder;
      $data .= str_repeat('=', $padlen);
    }
    return base64_decode(strtr($data, '-_', '+/'));
  }

  private static function sign(string $data, string $secret, Algorithm $alg)
  {
    switch ($alg) {
      case Algorithm::HS256:
        return hash_hmac('sha256', $data, $secret, true);
      case Algorithm::HS384:
        return hash_hmac('sha384', $data, $secret, true);
      case Algorithm::HS512:
        return hash_hmac('sha512', $data, $secret, true);
      default:
        throw new \InvalidArgumentException("Unsupported algorithm: $alg");
    }
  }

  public static function jwtEncode(array $payload, string $secret, Algorithm $alg = Algorithm::HS256)
  {
    $header = ['typ' => 'JWT', 'alg' => $alg];
    $segments = [
      self::base64UrlEncode(json_encode($header)),
      self::base64UrlEncode(json_encode($payload))
    ];
    $signingInput = implode('.', $segments);
    $signature = self::sign($signingInput, $secret, $alg);
    $segments[] = self::base64UrlEncode($signature);
    return implode('.', $segments);
  }

  public static function jwtDecode(string $jwt, string $secret, Algorithm $alg = Algorithm::HS256)
  {
    $segments = explode('.', $jwt);
    if (count($segments) !== 3) {
      throw new \InvalidArgumentException("Invalid JWT format");
    }

    [$headerB64, $payloadB64, $signatureB64] = $segments;
    $header = json_decode(self::base64UrlDecode($headerB64), true);
    $payload = json_decode(self::base64UrlDecode($payloadB64), true);
    $signature = self::base64UrlDecode($signatureB64);

    if (!is_array($header) || !isset($header['alg']) || !is_array($payload)) {
      throw new \InvalidArgumentException("Invalid JWT format");
    }

    if ($header['alg'] !== $alg->value) {
      throw new \InvalidArgumentException("Algorithm mismatch");
    }

    $signingInput = "$headerB64.$payloadB64";
    $expectedSignature = self::sign($signingInput, $secret, $alg);

    if (!hash_equals($expectedSignature, $signature)) {
      throw new \InvalidArgumentException("Invalid signature");
    }

    if (isset($payload['exp']) && time() >= $payload['exp']) {
      throw new \InvalidArgumentException("Token expired");
    }

    return $payload;
  }
}
