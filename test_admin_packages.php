<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TourPackage;
use App\Models\Destination;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

$adminUser = User::where('role', 'admin')->first() ?? User::first();
Auth::login($adminUser);
View::share('errors', new \Illuminate\Support\ViewErrorBag());

$packages = TourPackage::query()
    ->with('destination:id,name')
    ->withCount('bookings')
    ->latest()
    ->paginate(10);
$destinations = Destination::orderBy('name')->get(['id', 'name']);

echo "Testing admin.packages.index render...\n";
$htmlIndex = View::make('admin.packages.index', compact('packages', 'destinations'))->render();
echo "admin.packages.index rendered OK! (" . strlen($htmlIndex) . " bytes)\n";

echo "Testing admin.packages.create render...\n";
$htmlCreate = View::make('admin.packages.create', compact('destinations'))->render();
echo "admin.packages.create rendered OK! (" . strlen($htmlCreate) . " bytes)\n";

$package = TourPackage::first() ?? new TourPackage([
    'title' => 'Sample Trip',
    'destination_id' => 1,
    'price' => 1000000,
    'duration_days' => 3,
    'quota' => 10,
    'start_date' => now(),
    'end_date' => now()->addDays(3),
    'description' => 'Test description',
    'is_published' => true,
]);
if (!$package->exists) {
    $package->id = 1;
    $package->setRelation('destination', new Destination(['name' => 'Bali']));
    $package->setRelation('images', collect());
    $package->setRelation('itineraries', collect());
    $package->setRelation('reviews', collect());
} else {
    $package->load([
        'destination',
        'itineraries' => fn ($q) => $q->orderBy('day'),
        'images',
        'reviews.user:id,name',
    ]);
    $package->loadCount('bookings');
}

echo "Testing admin.packages.edit render...\n";
$htmlEdit = View::make('admin.packages.edit', compact('package', 'destinations'))->render();
echo "admin.packages.edit rendered OK! (" . strlen($htmlEdit) . " bytes)\n";

echo "Testing admin.packages.show render...\n";
$htmlShow = View::make('admin.packages.show', compact('package'))->render();
echo "admin.packages.show rendered OK! (" . strlen($htmlShow) . " bytes)\n";

