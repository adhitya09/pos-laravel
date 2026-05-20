<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $roles = Role::paginate($perPage)->withQueryString();
        return view('pages.role.index', compact('roles'));
    }

    public function create()
    {
        $permissionGroups = $this->permissionGroups();
        return view('pages.role.create', compact('permissionGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
        ]);

        $data = $request->only(['name', 'description']);
        $data['permissions'] = collect($request->get('permissions', []))
            ->filter(fn ($permission) => in_array($permission, $this->allowedPermissions(), true))
            ->values()
            ->all();

        Role::create($data);

        return redirect()->route('role.index')->with('success', 'Role berhasil ditambahkan');
    }

    public function edit(Role $role)
    {
        $permissionGroups = $this->permissionGroups();
        return view('pages.role.edit', compact('role', 'permissionGroups'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
        ]);

        $data = $request->only(['name', 'description']);
        $data['permissions'] = collect($request->get('permissions', []))
            ->filter(fn ($permission) => in_array($permission, $this->allowedPermissions(), true))
            ->values()
            ->all();

        $role->update($data);

        return redirect()->route('role.index')->with('success', 'Role berhasil diupdate');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('role.index')->with('success', 'Role berhasil dihapus');
    }

    /**
     * Return the permission groups used in the role editor.
     * Keep this centralized to avoid duplicating the permission list in views.
     *
     * @return array
     */
    protected function permissionGroups(): array
    {
        return [
            [
                'resource' => 'dashboard',
                'label' => 'Dashboard',
                'actions' => [
                    'viewAny' => 'Lihat',
                ],
            ],
            [
                'resource' => 'pos',
                'label' => 'POS',
                'actions' => [
                    'viewAny' => 'Lihat',
                ],
            ],
            [
                'resource' => 'kategori',
                'label' => 'Kategori',
                'actions' => [
                    'viewAny' => 'Lihat',
                ],
            ],
            [
                'resource' => 'produk',
                'label' => 'Produk',
                'actions' => [
                    'viewAny' => 'Lihat',
                ],
            ],
            [
                'resource' => 'inventory',
                'label' => 'Inventory',
                'actions' => [
                    'viewAny' => 'Lihat',
                ],
            ],
            [
                'resource' => 'payment-method',
                'label' => 'Metode Pembayaran',
                'actions' => [
                    'viewAny' => 'Lihat',
                    'restore' => 'Restore',
                ],
            ],
            [
                'resource' => 'transaksi',
                'label' => 'Transaksi',
                'actions' => [
                    'viewAny' => 'Lihat',
                    'update' => 'Ubah',
                ],
            ],
            [
                'resource' => 'cash-flow',
                'label' => 'Cash Flow',
                'actions' => [
                    'viewAny' => 'Lihat',
                ],
            ],
            [
                'resource' => 'report',
                'label' => 'Laporan',
                'actions' => [
                    'viewAny' => 'Lihat',
                ],
            ],
            [
                'resource' => 'user',
                'label' => 'User',
                'actions' => [
                    'viewAny' => 'Lihat',
                    'update' => 'Ubah',
                ],
            ],
            [
                'resource' => 'role',
                'label' => 'Role',
                'actions' => [
                    'viewAny' => 'Lihat',
                    'create' => 'Buat',
                    'update' => 'Ubah',
                    'delete' => 'Hapus',
                ],
            ],
            [
                'resource' => 'setting',
                'label' => 'Setting',
                'actions' => [
                    'viewAny' => 'Lihat',
                    'update' => 'Ubah',
                ],
            ],
        ];
    }

    protected function allowedPermissions(): array
    {
        return collect($this->permissionGroups())
            ->flatMap(function ($group) {
                return collect($group['actions'])
                    ->keys()
                    ->map(fn ($actionKey) => $group['resource'] . '.' . $actionKey);
            })
            ->push('*')
            ->values()
            ->all();
    }
}
