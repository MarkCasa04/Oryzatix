<?php

$cf = sys_get_temp_dir() . '/oryzatix_origin.txt';
@unlink($cf);

function xsrf($cf) {
    preg_match('/XSRF-TOKEN\s+([^\s]+)/', file_get_contents($cf), $m);
    return urldecode($m[1] ?? '');
}

function api($url, $cf, $method = 'GET', $body = null, $extraHeaders = []) {
    $headers = array_merge([
        'Accept: application/json',
        'X-Requested-With: XMLHttpRequest',
        'Origin: http://127.0.0.1:8000',
        'Referer: http://127.0.0.1:8000/home',
        'X-XSRF-TOKEN: ' . xsrf($cf),
    ], $extraHeaders);

    $ch = curl_init($url);
    $opts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEJAR => $cf,
        CURLOPT_COOKIEFILE => $cf,
        CURLOPT_HTTPHEADER => $headers,
    ];
    if ($method === 'POST') {
        $opts[CURLOPT_POST] = true;
        $opts[CURLOPT_POSTFIELDS] = $body;
    }
    curl_setopt_array($ch, $opts);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [$code, $resp];
}

api('http://127.0.0.1:8000/sanctum/csrf-cookie', $cf);

$email = 'origin_' . time() . '@test.com';
[$regCode, $regResp] = api(
    'http://127.0.0.1:8000/api/v1/auth/register',
    $cf,
    'POST',
    json_encode([
        'name' => 'Origin Test',
        'email' => $email,
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
        'role' => 'farmer',
        'device' => 'web',
    ]),
    ['Content-Type: application/json']
);
echo "Register: $regCode\n$regResp\n\n";

[$userCode, $userResp] = api('http://127.0.0.1:8000/api/v1/auth/user', $cf);
echo "User: $userCode\n$userResp\n\n";

$imgPath = sys_get_temp_dir() . '/leaf.jpg';
file_put_contents($imgPath, base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAn/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwCwAA//2Q=='));

[$upCode, $upResp] = api(
    'http://127.0.0.1:8000/api/v1/rice-detector/upload',
    $cf,
    'POST',
    ['image' => new CURLFile($imgPath, 'image/jpeg', 'healthy_leaf.jpg')]
);
echo "Upload: $upCode\n$upResp\n\n";

[$histCode, $histResp] = api('http://127.0.0.1:8000/api/v1/rice-detector/history', $cf);
echo "History: $histCode\n$histResp\n";

@unlink($imgPath);
@unlink($cf);
