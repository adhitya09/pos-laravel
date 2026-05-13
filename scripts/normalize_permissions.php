<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Role;

function normalizeAction(string $action): string
{
    $a = strtolower($action);
    $map = [
        'view' => 'viewAny',
        'index' => 'viewAny',
        'access' => 'viewAny',
        'detail' => 'view',
        'show' => 'view',
    ];

    if (isset($map[$a])) return $map[$a];

    // convert hyphen/underscore to camelCase (e.g. force-delete -> forceDelete)
    if (str_contains($a, '-') || str_contains($a, '_')) {
        $parts = preg_split('/[-_]/', $a);
        $first = array_shift($parts);
        $camel = $first;
        foreach ($parts as $p) {
            $camel .= ucfirst($p);
        }
        return $camel;
    }

    return $a;
}

function normalizePermission(string $perm): string
{
    if (! str_contains($perm, '.')) return $perm;
    [$resource, $action] = explode('.', $perm, 2);
    $normalizedAction = normalizeAction($action);
    return $resource . '.' . $normalizedAction;
}

$roles = Role::all();
$changes = 0;
foreach ($roles as $role) {
    $original = $role->permissions ?? [];
    $normalized = [];
    foreach ($original as $p) {
        if ($p === '*') {
            $normalized[] = '*';
            continue;
        }
        $normalized[] = normalizePermission($p);
    }
    $normalized = array_values(array_unique($normalized));

    // compare
    if ($normalized !== ($original ?? [])) {
        echo "Updating role '{$role->name}' (id={$role->id})\n";
        echo " - original: " . json_encode($original) . "\n";
        echo " - normalized: " . json_encode($normalized) . "\n";
        $role->permissions = $normalized;
        $role->save();
        $changes++;
    }
}

if ($changes === 0) {
    echo "No changes needed.\n";
} else {
    echo "Updated {$changes} roles.\n";
}
