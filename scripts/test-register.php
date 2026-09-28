<?php

$cookieFile = sys_get_temp_dir() . '/oryzatix_test_cookies.txt';
@unlink($cookieFile);

$ch = curl_init('http://127.0.0.1:8000/sanctum/csrf-cookie');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR => $cookieFile,
    CURLOPT_COOKIEFILE => $cookieFile,
]);
curl_exec($ch);

$xsrf = null;
if (file_exists($cookieFile)) {
    $cookies = file_get_contents($cookieFile);
    if (preg_match('/XSRF-TOKEN\s+([^\s]+)/', $cookies, $m)) {
        $xsrf = urldecode($m[1]);
    }
}

$email = 'dbtest_' . time() . '@test.com';
$body = json_encode([
    'name' => 'DB Test User',
    'email' => $email,
    'password' => 'secret123',
    'password_confirmation' => 'secret123',
    'role' => 'farmer',
    'location' => 'Roxas',
    'device' => 'web',
]);

curl_setopt_array($ch, [
    CURLOPT_URL => 'http://127.0.0.1:8000/api/v1/auth/register',
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $body,
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'Content-Type: application/json',
        'X-Requested-With: XMLHttpRequest',
        'X-XSRF-TOKEN: ' . $xsrf,
    ],
]);
$response = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP $code\n$response\n";

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$user = App\Models\User::where('email', $email)->first();
echo $user ? "DB SAVED: user #{$user->id} {$user->email}\n" : "DB NOT FOUND\n";
