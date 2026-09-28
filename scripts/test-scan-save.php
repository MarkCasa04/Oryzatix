<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\RiceScan;
use Illuminate\Support\Facades\Auth;

$user = User::first();
if (!$user) {
    echo "No users.\n";
    exit(1);
}

Auth::login($user);

RiceScan::query()->where('user_id', $user->id)->delete();

RiceScan::create([
    'user_id' => $user->id,
    'image_path' => 'scans/demo1.jpg',
    'disease_name' => 'Leaf Blast',
    'scientific_name' => 'Magnaporthe oryzae',
    'confidence' => 92,
    'severity' => 'severe',
    'treatment_recommendation' => ['chemical' => [], 'organic' => []],
]);

RiceScan::create([
    'user_id' => $user->id,
    'image_path' => 'scans/demo2.jpg',
    'disease_name' => 'Healthy',
    'scientific_name' => 'Oryza sativa',
    'confidence' => 88,
    'severity' => 'healthy',
    'treatment_recommendation' => ['chemical' => [], 'organic' => []],
]);

RiceScan::create([
    'user_id' => $user->id,
    'image_path' => 'scans/demo3.jpg',
    'disease_name' => 'Brown Spot',
    'scientific_name' => 'Cochliobolus miyabeanus',
    'confidence' => 75,
    'severity' => 'moderate',
    'treatment_recommendation' => ['chemical' => [], 'organic' => []],
]);

RiceScan::create([
    'user_id' => $user->id,
    'image_path' => 'scans/demo4.jpg',
    'disease_name' => 'Leaf Smut',
    'scientific_name' => 'Entyloma oryzae',
    'confidence' => 68,
    'severity' => 'mild',
    'treatment_recommendation' => ['chemical' => [], 'organic' => []],
]);

$controller = app(App\Http\Controllers\RiceScanController::class);
$history = json_decode($controller->history()->getContent(), true);

echo json_encode($history['stats'], JSON_PRETTY_PRINT) . PHP_EOL;
echo 'scan groups: ' . count($history['scans']) . PHP_EOL;

$ok = ($history['stats']['healthy'] ?? 0) === 1
    && ($history['stats']['moderate'] ?? 0) === 2
    && ($history['stats']['severe'] ?? 0) === 1
    && ($history['stats']['total'] ?? 0) === 4;

echo $ok ? "PASS\n" : "FAIL\n";
exit($ok ? 0 : 1);
