<?php

$cf = sys_get_temp_dir() . '/oryzatix_sess.txt';
@unlink($cf);

$ch = curl_init('http://127.0.0.1:8000/sanctum/csrf-cookie');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR => $cf,
    CURLOPT_COOKIEFILE => $cf,
]);
curl_exec($ch);
curl_close($ch);

function xsrf($cf) {
    preg_match('/XSRF-TOKEN\s+([^\s]+)/', file_get_contents($cf), $m);
    return urldecode($m[1] ?? '');
}

$email = 'sess_' . time() . '@test.com';
$body = json_encode([
    'name' => 'Session Test',
    'email' => $email,
    'password' => 'secret123',
    'password_confirmation' => 'secret123',
    'role' => 'farmer',
    'device' => 'web',
]);

$ch = curl_init('http://127.0.0.1:8000/api/v1/auth/register');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR => $cf,
    CURLOPT_COOKIEFILE => $cf,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $body,
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'Content-Type: application/json',
        'X-Requested-With: XMLHttpRequest',
        'X-XSRF-TOKEN: ' . xsrf($cf),
    ],
]);
$reg = curl_exec($ch);
echo 'Register: ' . curl_getinfo($ch, CURLINFO_HTTP_CODE) . "\n$reg\n\n";
curl_close($ch);

$ch = curl_init('http://127.0.0.1:8000/api/v1/auth/user');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR => $cf,
    CURLOPT_COOKIEFILE => $cf,
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'X-Requested-With: XMLHttpRequest',
        'X-XSRF-TOKEN: ' . xsrf($cf),
    ],
]);
$user = curl_exec($ch);
echo 'User: ' . curl_getinfo($ch, CURLINFO_HTTP_CODE) . "\n$user\n\n";
curl_close($ch);

echo "Cookie jar:\n" . file_get_contents($cf);

@unlink($cf);
