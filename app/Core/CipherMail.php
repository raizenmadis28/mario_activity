<?php
namespace App\Core;

class CipherMail
{
    const URL = 'https://violet-mantis-673635.hostingersite.com/ciphermail/api/send';
    const KEY = 'mdc_ciphermail_2f202e244d82bcfc4bbcee2b53d2f0fea83f67e6';

    public static function send(array $payload): array
    {
        if (!function_exists('curl_init')) {
            return ['ok' => false, 'error' => 'cURL not available', 'data' => []];
        }
        $ch = curl_init(self::URL);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Accept: application/json'],
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);
        $response = curl_exec($ch);
        $code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            return ['ok' => false, 'error' => 'cURL: ' . $curlErr, 'data' => []];
        }
        $data = json_decode($response, true);
        if ($code !== 200) {
            $err = is_array($data) && isset($data['error']) ? $data['error'] : ('HTTP ' . $code);
            return ['ok' => false, 'error' => $err, 'data' => $data];
        }
        return ['ok' => true, 'error' => '', 'data' => $data];
    }

    public static function sendOtp(string $to, string $username, string $code): array
    {
        $html = "
        <div style='font-family:Arial,sans-serif;max-width:540px;margin:0 auto;background:#f3f4f6'>
          <div style='background:linear-gradient(135deg,#e52521,#ff6b35);padding:24px;text-align:center;border-radius:12px 12px 0 0'>
            <h1 style='color:#fff;margin:0'>🍄 RaizenMadis28</h1>
            <p style='color:#ffeb3b;margin:8px 0 0;font-weight:bold'>Password Reset</p>
          </div>
          <div style='background:#fff;padding:28px;border-radius:0 0 12px 12px;border:1px solid #e5e7eb'>
            <p>Kumusta <strong>" . htmlspecialchars($username) . "</strong>,</p>
            <p>Nakatanggap kami ng kahilingan na i-reset ang iyong password. Ilagay ang code sa verification page:</p>
            <div style='text-align:center;background:#fef2f2;border:2px dashed #e52521;border-radius:12px;padding:20px;margin:20px 0'>
              <div style='font-size:11px;color:#6b7280;letter-spacing:2px'>YOUR VERIFICATION CODE</div>
              <div style='font-size:42px;letter-spacing:10px;font-weight:900;color:#e52521;font-family:monospace'>{$code}</div>
            </div>
            <p style='color:#6b7280;font-size:13px;text-align:center'>⏱️ Mag-eexpire sa 2 minuto.</p>
          </div>
        </div>";

        return self::send([
            'project_key'    => self::KEY,
            'to'             => $to,
            'subject'        => '🔐 Password Reset Code - RaizenMadis28',
            'body'           => $html,
            'alt_body'       => "Ang code mo ay {$code}. Expire sa 2 min.",
            'from_name'      => 'RaizenMadis28 Support',
            'recipient_name' => $username,
        ]);
    }
}