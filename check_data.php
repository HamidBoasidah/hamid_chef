<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "فحص البيانات:\n";
echo "========================================\n\n";

echo "Chefs: " . App\Models\Chef::count() . "\n";
echo "Addresses: " . App\Models\Address::count() . "\n";
echo "Addresses with coordinates: " . App\Models\Address::whereNotNull('lat')->whereNotNull('lang')->count() . "\n\n";

echo "Checking chef-address relationship:\n";
echo "----------------------------------------\n";

$chef = App\Models\Chef::first();
if ($chef) {
    echo "Chef: {$chef->name} (user_id: {$chef->user_id})\n";
    $address = App\Models\Address::where('user_id', $chef->user_id)
        ->where('is_default', true)
        ->where('is_active', true)
        ->first();

    if ($address) {
        echo "Has default address: Yes\n";
        echo "Coordinates: lat={$address->lat}, lng={$address->lang}\n";
    } else {
        echo "Has default address: No\n";

        // Check any address
        $anyAddress = App\Models\Address::where('user_id', $chef->user_id)->first();
        if ($anyAddress) {
            echo "Has any address: Yes (but not default or not active)\n";
            echo "is_default: {$anyAddress->is_default}, is_active: {$anyAddress->is_active}\n";
        } else {
            echo "Has any address: No\n";
        }
    }
}

echo "\n\nChecking all chefs with addresses:\n";
echo "----------------------------------------\n";

$chefsWithAddresses = App\Models\Chef::whereHas('user.addresses', function($q) {
    $q->whereNotNull('lat')
      ->whereNotNull('lang')
      ->where('is_active', true)
      ->where('is_default', true);
})->count();

echo "Chefs with valid addresses: {$chefsWithAddresses}\n";
