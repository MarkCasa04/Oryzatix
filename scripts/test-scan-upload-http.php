<?php

$cookieFile = sys_get_temp_dir() . '/oryzatix_upload_test.txt';
@unlink($cookieFile);

function curlRequest($url, $cookieFile, $xsrf, $method = 'GET', $body = null, $headers = []) {
    $ch = curl_init($url);
    $defaultHeaders = [
        'Accept: application/json',
        'X-Requested-With: XMLHttpRequest',
    ];
    if ($xsrf) {
        $defaultHeaders[] = 'X-XSRF-TOKEN: ' . $xsrf;
    }
    $opts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEJAR => $cookieFile,
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_HTTPHEADER => array_merge($defaultHeaders, $headers),
    ];
    if ($method === 'POST') {
        $opts[CURLOPT_POST] = true;
        $opts[CURLOPT_POSTFIELDS] = $body;
    }
    curl_setopt_array($ch, $opts);
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [$code, $response];
}

function readXsrf($cookieFile) {
    if (!file_exists($cookieFile)) return null;
    $cookies = file_get_contents($cookieFile);
    if (preg_match('/XSRF-TOKEN\s+([^\s]+)/', $cookies, $m)) {
        return urldecode($m[1]);
    }
    return null;
}

curlRequest('http://127.0.0.1:8000/sanctum/csrf-cookie', $cookieFile, null);
$xsrf = readXsrf($cookieFile);

$email = 'uploadtest_' . time() . '@test.com';
$registerBody = json_encode([
    'name' => 'Upload Test',
    'email' => $email,
    'password' => 'secret123',
    'password_confirmation' => 'secret123',
    'role' => 'farmer',
    'device' => 'web',
]);

[$regCode, $regResp] = curlRequest(
    'http://127.0.0.1:8000/api/v1/auth/register',
    $cookieFile,
    readXsrf($cookieFile),
    'POST',
    $registerBody,
    ['Content-Type: application/json']
);
echo "Register HTTP $regCode\n";

$imgPath = sys_get_temp_dir() . '/healthy_leaf.jpg';
file_put_contents($imgPath, base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAn/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwCwAA//2Q=='));

$cfile = new CURLFile($imgPath, 'image/jpeg', 'healthy_leaf.jpg');
[$upCode, $upResp] = curlRequest(
    'http://127.0.0.1:8000/api/v1/rice-detector/upload',
    $cookieFile,
    readXsrf($cookieFile),
    'POST',
    ['image' => $cfile]
);
echo "Upload HTTP $upCode\n$upResp\n";

[$histCode, $histResp] = curlRequest(
    'http://127.0.0.1:8000/api/v1/rice-detector/history',
    $cookieFile,
    readXsrf($cookieFile)
);
echo "History HTTP $histCode\n$histResp\n";

@unlink($imgPath);
@unlink($cookieFile);
