<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\CashboxFlow;
use App\Models\CashFlowSource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class CashFlowTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        $role = Role::create([
            'name' => 'Finance',
            'permissions' => [
                'cash-flow.viewAny',
                'cash-flow.create',
                'cash-flow.delete',
            ],
        ]);

        return User::create([
            'name' => 'Finance User',
            'email' => 'finance@test.com',
            'password' => Hash::make('password123'),
            'role_id' => $role->id,
        ]);
    }

    private function createInSource(): CashFlowSource
    {
        return CashFlowSource::create([
            'name' => 'Penjualan',
            'type' => 'in',
            'description' => 'Pemasukan',
        ]);
    }

    private function createOutSource(): CashFlowSource
    {
        return CashFlowSource::create([
            'name' => 'Operasional',
            'type' => 'out',
            'description' => 'Pengeluaran',
        ]);
    }

    public function test_user_can_create_cash_in_flow()
    {
        $user = $this->createUser();

        $source = $this->createInSource();

        $this->actingAs($user);

        $response = $this->post('/cash-flow', [
            'type' => 'in',
            'source_id' => $source->id,
            'amount' => 100000,
            'date' => now()->format('Y-m-d'),
            'notes' => 'Pemasukan harian',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('cashbox_flows', [
            'type' => 'in',
            'amount' => 100000,
        ]);
    }

    public function test_user_can_create_cash_out_flow()
    {
        $user = $this->createUser();

        $source = $this->createOutSource();

        $this->actingAs($user);

        $response = $this->post('/cash-flow', [
            'type' => 'out',
            'source_id' => $source->id,
            'amount' => 50000,
            'date' => now()->format('Y-m-d'),
            'notes' => 'Biaya listrik',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('cashbox_flows', [
            'type' => 'out',
            'amount' => 50000,
        ]);
    }

    public function test_cash_flow_fails_when_source_type_mismatch()
    {
        $user = $this->createUser();

        $source = $this->createInSource();

        $this->actingAs($user);

        $response = $this->post('/cash-flow', [
            'type' => 'out',
            'source_id' => $source->id,
            'amount' => 100000,
            'date' => now()->format('Y-m-d'),
            'notes' => 'Invalid source',
        ]);

        $response->assertSessionHasErrors();
    }

    public function test_manual_cashflow_can_be_deleted()
    {
        $user = $this->createUser();

        $source = $this->createInSource();

        $cashflow = CashboxFlow::create([
            'type' => 'in',
            'source_id' => $source->id,
            'amount' => 100000,
            'date' => now(),
            'notes' => 'Manual',
            'is_auto' => false,
        ]);

        $this->actingAs($user);

        $response = $this->delete('/cash-flow/' . $cashflow->id);

        $response->assertStatus(302);

        $this->assertSoftDeleted('cashbox_flows', [
            'id' => $cashflow->id,
        ]);
    }

    public function test_auto_cashflow_cannot_be_deleted()
    {
        $user = $this->createUser();

        $source = $this->createInSource();

        $cashflow = CashboxFlow::create([
            'type' => 'in',
            'source_id' => $source->id,
            'amount' => 100000,
            'date' => now(),
            'notes' => 'Auto',
            'is_auto' => true,
        ]);

        $this->actingAs($user);

        $response = $this->delete('/cash-flow/' . $cashflow->id);

        $response->assertSessionHas('error');

        $this->assertDatabaseHas('cashbox_flows', [
            'id' => $cashflow->id,
            'deleted_at' => null,
        ]);
    }
}
