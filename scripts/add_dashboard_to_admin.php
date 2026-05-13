<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Role;

$role = Role::where('name', 'Admin')->first();
if (! $role) {
    echo "Admin role not found.\n";
    exit(1);
}
$perms = $role->permissions ?? [];
if (in_array('dashboard.viewAny', $perms, true)) {
    echo "Admin already has dashboard.viewAny\n";
    exit(0);
}
$perms[] = 'dashboard.viewAny';
$role->permissions = array_values(array_unique($perms));
$role->save();
echo "Added dashboard.viewAny to Admin role (id={$role->id}).\n";
