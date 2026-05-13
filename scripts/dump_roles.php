<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Safely fetch roles
$roles = [];
try {
    $all = App\Models\Role::all();
    foreach ($all as $r) {
        $roles[] = [
            'id' => $r->id,
            'name' => $r->name,
            'permissions' => $r->permissions,
        ];
    }
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

echo json_encode($roles, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
