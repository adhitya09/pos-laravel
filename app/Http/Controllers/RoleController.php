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
        // Ensure permissions is always an array (empty when none selected)
        $data['permissions'] = $request->get('permissions', []);

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
        // If permissions not present in request, treat as empty array (unchecked)
        $data['permissions'] = $request->get('permissions', []);

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
        // Define normalized permission keys and labels.
        $standardActions = [
            'viewAny' => 'Lihat / Daftar',
            'view' => 'Detail',
            'create' => 'Buat',
            'update' => 'Ubah',
            'delete' => 'Hapus',
            'restore' => 'Restore',
            'forceDelete' => 'Force Delete',
            'export' => 'Export',
        ];

        // Resources and optional custom action sets (dashboard uses only view)
        $resources = [
            // Dashboard listing should use the standardized `viewAny` action
            ['resource' => 'dashboard', 'label' => 'Dashboard', 'actions' => ['viewAny' => 'Lihat']],
            ['resource' => 'pos', 'label' => 'POS'],
            ['resource' => 'kategori', 'label' => 'Kategori'],
            ['resource' => 'produk', 'label' => 'Produk'],
            ['resource' => 'inventory', 'label' => 'Inventory'],
            ['resource' => 'payment-method', 'label' => 'Metode Pembayaran'],
            ['resource' => 'transaksi', 'label' => 'Transaksi'],
            ['resource' => 'cash-flow', 'label' => 'Cash Flow'],
            ['resource' => 'report', 'label' => 'Laporan'],
            ['resource' => 'user', 'label' => 'User'],
            ['resource' => 'role', 'label' => 'Role'],
            ['resource' => 'setting', 'label' => 'Setting'],
            ['resource' => 'profile', 'label' => 'Profile'],
        ];

        $groups = [];
        foreach ($resources as $res) {
            $groups[] = [
                'resource' => $res['resource'],
                'label' => $res['label'],
                'actions' => $res['actions'] ?? $standardActions,
            ];
        }

        return $groups;
    }
}
