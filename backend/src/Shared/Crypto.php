<?php
namespace App\Shared;

enum Algorithm: string
{
    case HS256 = 'HS256';
    case HS384 = 'HS384';
    case HS512 = 'HS512';
}

class Crypto
{
    public static function generateRandomString(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $data .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }

    private static function sign(string $data, string $secret, Algorithm $alg): string
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

    /**
     * @param array<string, mixed> $payload
     */
    public static function jwtEncode(array $payload, string $secret, Algorithm $alg = Algorithm::HS256): string
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

    /**
     * @return array<string, mixed>
     */
    public static function jwtDecode(string $jwt, string $secret, Algorithm $alg = Algorithm::HS256): array
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

    private const ENCRYPTION_PREFIX = 'gcm1:';

    /**
     * AES-256-GCMで値を暗号化する。DBに機微な第三者トークンを保存する際に使用する。
     */
    public static function encrypt(string $plaintext, string $key): string
    {
        $binKey = self::deriveEncryptionKey($key);
        $iv = random_bytes(12);
        $tag = '';
        $ciphertext = openssl_encrypt($plaintext, 'aes-256-gcm', $binKey, OPENSSL_RAW_DATA, $iv, $tag);
        if ($ciphertext === false) {
            throw new \RuntimeException('Failed to encrypt value');
        }

        return self::ENCRYPTION_PREFIX . self::base64UrlEncode($iv . $tag . $ciphertext);
    }

    /**
     * encrypt() で暗号化した値を復号する。
     */
    public static function decrypt(string $encoded, string $key): string
    {
        $binKey = self::deriveEncryptionKey($key);
        $raw = self::base64UrlDecode(substr($encoded, strlen(self::ENCRYPTION_PREFIX)));
        $iv = substr($raw, 0, 12);
        $tag = substr($raw, 12, 16);
        $ciphertext = substr($raw, 28);

        $plaintext = openssl_decrypt($ciphertext, 'aes-256-gcm', $binKey, OPENSSL_RAW_DATA, $iv, $tag);
        if ($plaintext === false) {
            throw new \RuntimeException('Failed to decrypt value');
        }

        return $plaintext;
    }

    /**
     * @return bool encrypt() で生成された形式の値かどうか。移行期間中に旧・平文レコードと
     * 新・暗号化レコードを区別するために使う。
     */
    public static function isEncrypted(?string $value): bool
    {
        return $value !== null && str_starts_with($value, self::ENCRYPTION_PREFIX);
    }

    private static function deriveEncryptionKey(string $key): string
    {
        // CRYPTO_KEY は環境変数由来の可変長文字列のため、AES-256に必要な32バイト鍵に正規化する。
        return hash('sha256', $key, true);
    }
}
